<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class DeviceTypes extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DeviceType_model');
    }

    /**
     * GET /api/device-types
     * Mengembalikan semua tipe perangkat dari tabel device_types
     */
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        $types = $this->DeviceType_model->getAll();
        return $this->successResponse($types, 'device types retrieved');
    }
}
