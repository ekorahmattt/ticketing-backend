<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Devices extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Device_model');
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
                $d_array['users'] = $this->DeviceUserAssignment_model->getUsersByDevice($d->id);
                $d_array['connections'] = $this->DeviceConnection_model->getConnections($d->id);
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

            // Pisahkan relasi users dan connections dari $data
            $users = isset($data['users']) ? $data['users'] : [];
            $connections = isset($data['connections']) ? $data['connections'] : [];

            // Hapus field yang tidak boleh diisi oleh client secara manual
            unset($data['users'], $data['connections'], $data['created_by'], $data['updated_by']);

            $data['created_at'] = date('Y-m-d H:i:s');

            // Set created_by dari header X-User-Id (diisi frontend setelah login)
            $userId = $this->input->get_request_header('X-User-Id', TRUE);
            if ($userId && is_numeric($userId)) {
                $data['created_by'] = (int)$userId;
            }

            // Insert Device
            $insertId = $this->Device_model->create($data);

            if ($insertId) {
                // Insert User Assignments
                foreach ($users as $user_id) {
                    $this->DeviceUserAssignment_model->assignUser($insertId, $user_id);
                }

                // Insert Device Connections
                foreach ($connections as $conn) {
                    $connData = [
                        'parent_device_id' => $insertId,
                        'child_device_id' => $conn['child_device_id'],
                        'connection_type' => isset($conn['connection_type']) ? $conn['connection_type'] : null
                    ];
                    $this->DeviceConnection_model->createConnection($connData);
                }

                // Get New Data with Relations
                $deviceData = $this->getDeviceDetail($insertId);
                return $this->successResponse($deviceData, 'device created');
            }

            return $this->errorResponse('failed to create device', 500);
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

            $device = $this->Device_model->getById($id);
            if (!$device) {
                return $this->errorResponse('device not found', 404);
            }

            $users = isset($data['users']) ? $data['users'] : null;
            $connections = isset($data['connections']) ? $data['connections'] : null;

            // Hapus field yang tidak boleh diisi oleh client secara manual
            unset($data['users'], $data['connections'], $data['created_by'], $data['updated_by']);

            // Update Tabel Devices jika ada data device utama
            if (!empty($data)) {
                $data['updated_at'] = date('Y-m-d H:i:s');

                // Set updated_by dari header X-User-Id
                $userId = $this->input->get_request_header('X-User-Id', TRUE);
                if ($userId && is_numeric($userId)) {
                    $data['updated_by'] = (int)$userId;
                }

                $this->Device_model->update($id, $data);
            }

            // Re-sync Users Assignments
            if ($users !== null) {
                $this->DeviceUserAssignment_model->deleteByDevice($id);
                foreach ($users as $user_id) {
                    $this->DeviceUserAssignment_model->assignUser($id, $user_id);
                }
            }

            // Re-sync Device Connections
            if ($connections !== null) {
                $this->DeviceConnection_model->deleteByDevice($id);
                foreach ($connections as $conn) {
                    $connData = [
                        'parent_device_id' => $id,
                        'child_device_id' => $conn['child_device_id'],
                        'connection_type' => isset($conn['connection_type']) ? $conn['connection_type'] : null
                    ];
                    $this->DeviceConnection_model->createConnection($connData);
                }
            }

            $updatedDevice = $this->getDeviceDetail($id);
            return $this->successResponse($updatedDevice, 'device updated');
        }

        // DELETE /api/devices/{id}
        elseif ($method === 'DELETE') {
            $device = $this->Device_model->getById($id);
            if (!$device) {
                return $this->errorResponse('device not found', 404);
            }

            // Hapus Relasi Terlebih Dahulu (Manual Cascade)
            $this->DeviceUserAssignment_model->deleteByDevice($id);
            $this->DeviceConnection_model->deleteByDevice($id);

            // Hapus Device
            if ($this->Device_model->delete($id)) {
                return $this->successResponse((object)[], 'device deleted');
            }

            return $this->errorResponse('failed to delete device', 500);
        }
        else {
            return $this->errorResponse('Method Not Allowed', 405);
        }
    }

    /**
     * Helper logic detail perangkat dengan relasinya
     */
    private function getDeviceDetail($id)
    {
        $device = $this->Device_model->getById($id);
        if (!$device) {
            return null;
        }

        $deviceArray = (array)$device;
        $deviceArray['users'] = $this->DeviceUserAssignment_model->getUsersByDevice($id);
        $deviceArray['connections'] = $this->DeviceConnection_model->getConnections($id);

        return $deviceArray;
    }
}
