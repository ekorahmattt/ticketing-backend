<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaseApiController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Konfigurasi Header untuk CORS (Cross-Origin Resource Sharing)
        // Dibutuhkan karena API akan diakses oleh frontend React (domain/port berbeda)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-User-Role, X-User-Id');

        // Handle request OPTIONS (Preflight dari browser)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Standard Success Response Format
     */
    protected function successResponse($data = [], $message = 'request successful')
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response));
    }

    /**
     * Standard Error Response Format
     */
    protected function errorResponse($message = 'something went wrong', $code = 400)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response));
    }
}
