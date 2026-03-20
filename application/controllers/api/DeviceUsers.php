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
     * GET /api/device-users
     * Mengembalikan semua device_users dengan unit_name untuk dropdown di frontend
     */
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        $users = $this->DeviceUser_model->getAll();
        return $this->successResponse($users, 'device users retrieved');
    }
}
