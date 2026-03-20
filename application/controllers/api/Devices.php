<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Devices extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Device_model');
        $this->load->model('DeviceType_model');
        $this->load->model('Unit_model');
        $this->load->model('DeviceUserAssignment_model');
        $this->load->model('DeviceConnection_model');
    }

    public function index()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // GET /api/devices
        if ($method === 'GET') {
            $devices = $this->Device_model->getAll();
            $result = [];

            foreach ($devices as $d) {
                $d_array = (array)$d;
                $d_array['users']            = $this->DeviceUserAssignment_model->getUsersByDevice($d->id);
                $d_array['connections']      = $this->DeviceConnection_model->getConnections($d->id);
                $d_array['parent_connections'] = $this->DeviceConnection_model->getParentDevices($d->id);
                $result[] = $d_array;
            }

            return $this->successResponse($result, 'devices retrieved');
        }

        // POST /api/devices
        elseif ($method === 'POST') {
            $json = $this->input->raw_input_stream;
            $data = json_decode($json, true);

            if (empty($data['device_name'])) {
                return $this->errorResponse('device_name is required', 400);
            }

            // Pisahkan relasi dari data utama
            $users       = isset($data['users'])       ? $data['users']       : [];
            $connections = isset($data['connections'])  ? $data['connections'] : [];

            // Resolve device_type_name → device_type_id dan unit_name → unit_id
            $deviceTypeId = $this->resolveDeviceTypeId($data);
            $unitId       = $this->resolveUnitId($data);

            // Bangun data yang hanya berisi kolom valid tabel devices
            $deviceData = $this->buildDeviceFields($data, $deviceTypeId, $unitId);
            $deviceData['created_at'] = date('Y-m-d H:i:s');

            $userId = $this->input->get_request_header('X-User-Id', TRUE);
            if ($userId && is_numeric($userId)) {
                $deviceData['created_by'] = (int)$userId;
            }

            $insertId = $this->Device_model->create($deviceData);

            if (!$insertId) {
                return $this->errorResponse('failed to create device', 500);
            }

            // Simpan User Assignments
            foreach ($users as $user_id) {
                if (is_numeric($user_id)) {
                    $this->DeviceUserAssignment_model->assignUser($insertId, (int)$user_id);
                }
            }

            // Simpan Device Connections
            // Frontend kirim: { parent_device_id: komputer_id, child_device_id: null/id }
            // Untuk device BARU (child_device_id = null), backend isi dengan insertId
            foreach ($connections as $conn) {
                $parentId = !empty($conn['parent_device_id']) ? (int)$conn['parent_device_id'] : null;
                // child = device yang baru dibuat jika null
                $childId  = !empty($conn['child_device_id'])  ? (int)$conn['child_device_id']  : $insertId;

                if ($parentId && $childId) {
                    $this->DeviceConnection_model->createConnection([
                        'parent_device_id' => $parentId,
                        'child_device_id'  => $childId,
                        'connection_type'  => isset($conn['connection_type']) ? $conn['connection_type'] : 'USB',
                    ]);
                }
            }

            $deviceDetail = $this->getDeviceDetail($insertId);
            return $this->successResponse($deviceDetail, 'device created');
        }

        else {
            return $this->errorResponse('Method Not Allowed', 405);
        }
    }

    public function show($id)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // GET /api/devices/{id}
        if ($method === 'GET') {
            $device = $this->getDeviceDetail($id);
            if ($device) {
                return $this->successResponse($device, 'device retrieved');
            }
            return $this->errorResponse('device not found', 404);
        }

        // PUT /api/devices/{id}
        elseif ($method === 'PUT') {
            $json = $this->input->raw_input_stream;
            $data = json_decode($json, true);

            if (!$this->Device_model->getById($id)) {
                return $this->errorResponse('device not found', 404);
            }

            // Pisahkan relasi dari data utama
            $users       = isset($data['users'])       ? $data['users']       : null;
            $connections = isset($data['connections'])  ? $data['connections'] : null;

            // Resolve device_type_name → device_type_id dan unit_name → unit_id
            $deviceTypeId = $this->resolveDeviceTypeId($data);
            $unitId       = $this->resolveUnitId($data);

            // Bangun data yang hanya berisi kolom valid tabel devices
            $deviceData = $this->buildDeviceFields($data, $deviceTypeId, $unitId);

            if (!empty($deviceData)) {
                $deviceData['updated_at'] = date('Y-m-d H:i:s');
                $userId = $this->input->get_request_header('X-User-Id', TRUE);
                if ($userId && is_numeric($userId)) {
                    $deviceData['updated_by'] = (int)$userId;
                }
                $this->Device_model->update($id, $deviceData);
            }

            // Re-sync User Assignments
            if ($users !== null) {
                $this->DeviceUserAssignment_model->deleteByDevice($id);
                foreach ($users as $user_id) {
                    if (is_numeric($user_id)) {
                        $this->DeviceUserAssignment_model->assignUser($id, (int)$user_id);
                    }
                }
            }

            // Re-sync Device Connections
            // Hapus semua koneksi di mana device ini adalah parent ATAU child
            if ($connections !== null) {
                $this->DeviceConnection_model->deleteAllByDevice($id);
                foreach ($connections as $conn) {
                    if (!empty($conn['parent_device_id']) && !empty($conn['child_device_id'])) {
                        $this->DeviceConnection_model->createConnection([
                            'parent_device_id' => (int)$conn['parent_device_id'],
                            'child_device_id'  => (int)$conn['child_device_id'],
                            'connection_type'  => isset($conn['connection_type']) ? $conn['connection_type'] : 'USB',
                        ]);
                    }
                }
            }

            $updatedDevice = $this->getDeviceDetail($id);
            return $this->successResponse($updatedDevice, 'device updated');
        }

        // DELETE /api/devices/{id}
        elseif ($method === 'DELETE') {
            if (!$this->Device_model->getById($id)) {
                return $this->errorResponse('device not found', 404);
            }

            $this->DeviceUserAssignment_model->deleteByDevice($id);
            $this->DeviceConnection_model->deleteAllByDevice($id);

            if ($this->Device_model->delete($id)) {
                return $this->successResponse((object)[], 'device deleted');
            }

            return $this->errorResponse('failed to delete device', 500);
        }

        else {
            return $this->errorResponse('Method Not Allowed', 405);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Resolve device_type_id dari nama (string) yang dikirim frontend.
     */
    private function resolveDeviceTypeId($data)
    {
        if (!empty($data['device_type_id']) && is_numeric($data['device_type_id'])) {
            return (int)$data['device_type_id'];
        }

        if (!empty($data['device_type'])) {
            $typeRow = $this->db->where('name', trim($data['device_type']))->get('device_types')->row();
            if ($typeRow) return (int)$typeRow->id;
        }

        return null;
    }

    /**
     * Resolve unit_id dari nama unit (string) yang dikirim frontend.
     * Frontend mengirim 'unit' berisi nama unit, DB menyimpan 'unit_id' sebagai FK integer.
     */
    private function resolveUnitId($data)
    {
        if (!empty($data['unit_id']) && is_numeric($data['unit_id'])) {
            return (int)$data['unit_id'];
        }

        if (!empty($data['unit'])) {
            $row = $this->Unit_model->getByName(trim($data['unit']));
            if ($row) return (int)$row->id;
        }

        return null;
    }

    /**
     * Bangun array hanya berisi kolom-kolom yang ada di tabel 'devices'.
     * Mencegah error SQL akibat field tak dikenal (status, location, unit varchar, dll).
     */
    private function buildDeviceFields($data, $deviceTypeId, $unitId = null)
    {
        $allowed = [
            'device_name', 'brand', 'model', 'serial_number',
            'ip_address', 'mac_address', 'remote_address',
            'os', 'coord_x', 'coord_y',
        ];

        $fields = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[$col] = $data[$col];
            }
        }

        // Handle device_type_id secara eksplisit
        if ($deviceTypeId !== null) {
            $fields['device_type_id'] = $deviceTypeId;
        }

        // Handle unit_id secara eksplisit
        if ($unitId !== null) {
            $fields['unit_id'] = $unitId;
        } elseif (array_key_exists('unit_id', $data) && is_numeric($data['unit_id'])) {
            $fields['unit_id'] = (int)$data['unit_id'];
        }

        return $fields;
    }

    /**
     * Helper: ambil detail device beserta relasi (users, connections, parent_connections)
     */
    private function getDeviceDetail($id)
    {
        $device = $this->Device_model->getById($id);
        if (!$device) {
            return null;
        }

        $deviceArray = (array)$device;
        $deviceArray['users']              = $this->DeviceUserAssignment_model->getUsersByDevice($id);
        $deviceArray['connections']        = $this->DeviceConnection_model->getConnections($id);
        $deviceArray['parent_connections'] = $this->DeviceConnection_model->getParentDevices($id);

        return $deviceArray;
    }
}
