<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Units extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Unit_model');
    }

    /**
     * GET /api/units
     * Mengembalikan semua unit untuk keperluan dropdown di frontend
     */
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        $units = $this->Unit_model->getAll();
        return $this->successResponse($units, 'units retrieved');
    }
}
