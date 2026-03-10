<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Memanggil BaseApiController yang berada di folder yang sama
require_once APPPATH . 'controllers/api/BaseApiController.php';

class Test extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [
            'server_time' => date('Y-m-d H:i:s')
        ];

        // Menggunakan helper method dari BaseApiController
        $this->successResponse($data, 'API Berhasil');
    }
}
