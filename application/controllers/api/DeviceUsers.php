<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class DeviceUsers extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DeviceUser_model');
    }

    /**
     * GET  /api/device-users         → daftar semua device_users (dengan unit_name)
     * POST /api/device-users         → tambah device_user baru
     */
    public function index()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // ── GET: Ambil semua device_users ──────────────────────────────────────
        if ($method === 'GET') {
            $users = $this->DeviceUser_model->getAll();
            return $this->successResponse($users, 'device users retrieved');
        }

        // ── POST: Tambah device_user baru ──────────────────────────────────────
        elseif ($method === 'POST') {
            $json = $this->input->raw_input_stream;
            $data = json_decode($json, true);

            if (empty($data['name'])) {
                return $this->errorResponse('Nama (username) wajib diisi.', 400);
            }

            $insertData = [
                'name'       => trim($data['name']),
                'full_name'  => isset($data['full_name'])  ? trim($data['full_name'])  : null,
                'unit_id'    => isset($data['unit_id'])    ? (int)$data['unit_id']     : null,
                'phone'      => isset($data['phone'])      ? trim($data['phone'])      : null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $insertId = $this->DeviceUser_model->create($insertData);
            if ($insertId) {
                $created = $this->DeviceUser_model->getById($insertId);
                return $this->successResponse($created, 'device user created');
            }

            return $this->errorResponse('Gagal menambahkan device user.', 500);
        }

        return $this->errorResponse('Method Not Allowed', 405);
    }

    /**
     * GET    /api/device-users/{id}  → detail device_user
     * PUT    /api/device-users/{id}  → update device_user
     * DELETE /api/device-users/{id}  → hapus device_user
     */
    public function show($id)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // ── GET: Detail device_user ────────────────────────────────────────────
        if ($method === 'GET') {
            $user = $this->DeviceUser_model->getById($id);
            if (!$user) {
                return $this->errorResponse('Device user tidak ditemukan.', 404);
            }
            return $this->successResponse($user, 'device user detail retrieved');
        }

        // ── PUT: Update device_user ────────────────────────────────────────────
        elseif ($method === 'PUT') {
            $user = $this->DeviceUser_model->getById($id);
            if (!$user) {
                return $this->errorResponse('Device user tidak ditemukan.', 404);
            }

            $json = $this->input->raw_input_stream;
            $data = json_decode($json, true);

            if (empty($data['name'])) {
                return $this->errorResponse('Nama (username) wajib diisi.', 400);
            }

            $updateData = [
                'name'      => trim($data['name']),
                'full_name' => isset($data['full_name']) ? trim($data['full_name']) : null,
                'unit_id'   => isset($data['unit_id'])   ? (int)$data['unit_id']   : null,
                'phone'     => isset($data['phone'])     ? trim($data['phone'])     : null,
            ];

            $this->DeviceUser_model->update($id, $updateData);
            $updated = $this->DeviceUser_model->getById($id);
            return $this->successResponse($updated, 'device user updated');
        }

        // ── DELETE: Hapus device_user ──────────────────────────────────────────
        elseif ($method === 'DELETE') {
            $user = $this->DeviceUser_model->getById($id);
            if (!$user) {
                return $this->errorResponse('Device user tidak ditemukan.', 404);
            }

            if ($this->DeviceUser_model->delete($id)) {
                return $this->successResponse((object)[], 'device user deleted');
            }

            return $this->errorResponse('Gagal menghapus device user.', 500);
        }

        return $this->errorResponse('Method Not Allowed', 405);
    }
}
