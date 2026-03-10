<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Auth extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        // Ambil data JSON dari raw input stream
        $json = $this->input->raw_input_stream;
        $data = json_decode($json, true);

        if (empty($data['username']) || empty($data['password'])) {
            return $this->errorResponse('invalid username or password', 400);
        }

        $username = $data['username'];
        $password = $data['password'];

        // Cari user berdasarkan username melalui User_model
        $user = $this->User_model->getByUsername($username);

        // Jika user ditemukan dan password verify cocok
        if ($user && password_verify($password, $user->password)) {
            // Update last_login time
            $this->User_model->updateLastLogin($user->id);

            // Siapkan response user dengan menyembunyikan field password
            $userData = [
                'id' => (int)$user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role
            ];

            return $this->successResponse($userData, 'login successful');
        }

        return $this->errorResponse('invalid username or password', 400);
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        // Dalam MVP ini, hanya return response success (Token penghapusan dibebankan pada Frontend jika pakai localSotrage dsb.)
        // Jika nantinya menggunakan session/token database, bisa dicabutt/dihapus di sini
        return $this->successResponse((object)[], 'logout successful');
    }

    public function createAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        $json = $this->input->raw_input_stream;
        $data = json_decode($json, true);

        // Validasi payload
        if (empty($data['name']) || empty($data['username']) || empty($data['password']) || empty($data['role'])) {
            return $this->errorResponse('incomplete data provided', 400);
        }

        // Cek apabila username sudah ada sebelumnya
        $existingUser = $this->User_model->getByUsername($data['username']);
        if ($existingUser) {
            return $this->errorResponse('username already exists', 400);
        }

        // Susun data untuk disimpan
        $insertData = [
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Buat user melalui User_model
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
}
