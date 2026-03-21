<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Categories extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');
    }

    /**
     * GET /api/categories → daftar semua kategori (untuk dropdown)
     */
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->errorResponse('Method Not Allowed', 405);
        }
        $data = $this->Category_model->getAll();
        return $this->successResponse($data, 'categories retrieved');
    }
}
