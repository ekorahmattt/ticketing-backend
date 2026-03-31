<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class AdminUsers extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');

        $role = $this->input->get_request_header('X-User-Role', TRUE);
        $method = $this->input->server('REQUEST_METHOD');

        // Allow both admin and superadmin to view admin list (GET)
        if ($method === 'GET') {
            if (empty($role) || !in_array(strtolower((string)$role), ['admin', 'superadmin'])) {
                echo json_encode(['status' => 'error', 'message' => 'access denied']);
                exit;
            }
        } 
        // Other methods (POST, PUT, DELETE) still require 'superadmin'
        else {
            if (empty($role) || strtolower((string)$role) !== 'superadmin') {
                echo json_encode(['status' => 'error', 'message' => 'access denied']);
                exit;
            }
        }
    }

    public function index()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // 1. MENDAPATKAN DAFTAR AKUN ADMIN (GET)
        if ($method === 'GET') {
            $users = $this->User_model->getAllAdmins();

            // Sembunyikan field password supaya tidak bocor ke publik
            foreach ($users as &$user) {
                unset($user->password);
            }

            return $this->successResponse($users, 'admin users retrieved');
        }

        // 3. MENAMBAHKAN AKUN ADMIN (POST)
        elseif ($method === 'POST') {
            $json = $this->input->raw_input_stream;
            $data = json_decode($json, true);

            if (empty($data['name']) || empty($data['username']) || empty($data['password']) || empty($data['role'])) {
                return $this->errorResponse('incomplete data provided', 400);
            }

            // Pengecekan username unik
            $existingUser = $this->User_model->getByUsername($data['username']);
            if ($existingUser) {
                return $this->errorResponse('username already exists', 400);
            }

            $insertData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => $data['role'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Trigger insert pada model
            $insertId = $this->User_model->create($insertData);

            if ($insertId) {
                $userData = [
                    'id' => (int)$insertId,
                    'name' => $insertData['name'],
                    'username' => $insertData['username'],
                    'role' => $insertData['role']
                ];
                return $this->successResponse($userData, 'admin user created');
            }

            return $this->errorResponse('failed to create admin user', 500);
        }
        else {
            return $this->errorResponse('Method Not Allowed', 405);
        }
    }

    public function show($id)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // 2. MENGAMBIL DETAIL ADMIN GET /api/admin-users/{id}
        if ($method === 'GET') {
            $user = $this->User_model->getById($id);
            if ($user) {
                unset($user->password);
                return $this->successResponse($user, 'admin detail retrieved');
            }
            return $this->errorResponse('admin user not found', 404);

        }

        // 4. MENGUPDATE AKUN ADMIN PUT /api/admin-users/{id}
        elseif ($method === 'PUT') {
            $json = $this->input->raw_input_stream;
            $data = json_decode($json, true);

            $user = $this->User_model->getById($id);
            if (!$user) {
                return $this->errorResponse('admin user not found', 404);
            }

            $updateData = [];

            if (!empty($data['name'])) {
                $updateData['name'] = $data['name'];
            }

            if (!empty($data['username'])) {
                $existingUser = $this->User_model->getByUsername($data['username']);
                // Tidak boleh duplicate username dengan admin lain
                if ($existingUser && $existingUser->id != $id) {
                    return $this->errorResponse('username already exists', 400);
                }
                $updateData['username'] = $data['username'];
            }

            if (!empty($data['role'])) {
                $updateData['role'] = $data['role'];
            }

            if (!empty($data['password'])) {
                $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (!empty($updateData)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                $this->User_model->update($id, $updateData);
            }

            $updatedUser = $this->User_model->getById($id);
            unset($updatedUser->password);

            return $this->successResponse($updatedUser, 'admin user updated');

        }

        // 5. MENGHAPUS AKUN ADMIN DELETE /api/admin-users/{id}
        elseif ($method === 'DELETE') {
            $user = $this->User_model->getById($id);
            if (!$user) {
                return $this->errorResponse('admin user not found', 404);
            }

            if ($this->User_model->delete($id)) {
                return $this->successResponse((object)[], 'admin user deleted');
            }

            return $this->errorResponse('failed to delete admin user', 500);
        }
        else {
            return $this->errorResponse('Method Not Allowed', 405);
        }
    }
}
