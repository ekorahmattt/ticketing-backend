<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Messages extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Message_model');
    }

    public function index($ticket_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode([
                'status' => 'error',
                'message' => 'Method Not Allowed'
            ]));
        }

        $messages = $this->Message_model->getMessagesByTicket($ticket_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
            'status' => 'success',
            'message' => 'messages retrieved',
            'data' => $messages
        ]));
    }

    public function send($ticket_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode([
                'status' => 'error',
                'message' => 'Method Not Allowed'
            ]));
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            $input = $this->input->post();
        }

        $sender_type = isset($input['sender_type']) ? $input['sender_type'] : null;
        $sender_id = isset($input['sender_id']) ? $input['sender_id'] : null;
        $message = isset($input['message']) ? $input['message'] : null;

        if (!$sender_type || !$message) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                'status' => 'error',
                'message' => 'sender_type and message are required'
            ]));
        }

        $data = [
            'ticket_id' => $ticket_id,
            'sender_type' => $sender_type,
            'sender_id' => $sender_type === 'device' ? null : $sender_id,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->Message_model->sendMessage($data);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
            'status' => 'success',
            'message' => 'message sent'
        ]));
    }
}
