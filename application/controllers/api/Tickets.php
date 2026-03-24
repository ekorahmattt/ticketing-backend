<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Tickets extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ticket_model');
        $this->load->model('Device_model');
        $this->load->model('TicketAttachment_model');
    }

    public function index()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method === 'GET') {
            $device_id = $this->input->get('device_id');
            if ($device_id) {
                $tickets = $this->Ticket_model->getTicketsByDeviceId($device_id);
            } else {
                $tickets = $this->Ticket_model->getAllTickets();
            }
            return $this->successResponse($tickets, 'Daftar ticket berhasil dimuat');
        }
        elseif ($method === 'POST') {
            return $this->createTicket();
        }
        else {
            return $this->errorResponse('Method Not Allowed', 405);
        }
    }

    private function createTicket()
    {
        $stream = $this->input->raw_input_stream;
        $postData = json_decode($stream, true);

        // Jika JSON gagal parsing, fallback ke normal $_POST / form-data
        if (!$postData) {
            $postData = $this->input->post();
        }

        $ip_address = $this->input->ip_address();
        $hostname = gethostbyaddr($ip_address);

        $data = [
            'device_id' => isset($postData['device_id']) ? $postData['device_id'] : null,
            'reporter_name' => isset($postData['reporter_name']) ? $postData['reporter_name'] : null,
            'reporter_unit' => isset($postData['reporter_unit']) ? $postData['reporter_unit'] : null,
            'reporter_contact' => isset($postData['reporter_contact']) ? $postData['reporter_contact'] : null,
            'report_hostname' => $hostname,
            'report_ip' => $ip_address,
            'report_device_brand' => isset($postData['report_device_brand']) ? $postData['report_device_brand'] : null,
            'report_device_model' => isset($postData['report_device_model']) ? $postData['report_device_model'] : null,
            'report_user_agent' => $this->input->user_agent(),
            'category_id' => isset($postData['category_id']) ? $postData['category_id'] : null,
            'subcategory_id' => isset($postData['subcategory_id']) ? $postData['subcategory_id'] : null,
            'title' => isset($postData['title']) ? $postData['title'] : null,
            'description' => isset($postData['description']) ? $postData['description'] : null,
            'status' => 'open', // Default status
            'created_at' => isset($postData['created_at']) ? $postData['created_at'] : date('Y-m-d H:i:s')
        ];
        $ticket_id = $this->Ticket_model->create($data);

        // Upload Screenshot
        if (isset($_FILES['screenshot']) && !empty($_FILES['screenshot']['name'])) {
            $upload_path = FCPATH . 'uploads/tickets/';
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name'] = 'ticket_' . $ticket_id . '_' . time();

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('screenshot')) {
                $upload_data = $this->upload->data();
                $attachment_data = [
                    'ticket_id' => $ticket_id,
                    'file_name' => $upload_data['file_name'],
                    'file_path' => '/uploads/tickets/' . $upload_data['file_name']
                ];
                $this->TicketAttachment_model->insert($attachment_data);
            }
        }

        $this->triggerWebSocket('ticket_created', ['ticket_id' => $ticket_id, 'status' => 'open']);

        $response_data = ['ticket_id' => $ticket_id];
        if (isset($attachment_data['file_path'])) {
            $response_data['attachment_path'] = $attachment_data['file_path'];
        }

        return $this->successResponse($response_data, 'ticket created');
    }

    public function detectDevice()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        $ip_address = $this->input->ip_address();
        $device = $this->Device_model->getByIp($ip_address);

        $response = ['status' => 'success'];
        if ($device) {
            $this->load->model('DeviceUserAssignment_model');
            $deviceDetail = $this->Device_model->getById($device->id);
            $users = $this->DeviceUserAssignment_model->getUsersByDevice($device->id);

            $response['device_detected'] = true;
            $response['data'] = [
                'device_id' => $deviceDetail->id,
                'device_name' => isset($deviceDetail->device_name) ? $deviceDetail->device_name : null,
                'hostname' => isset($deviceDetail->hostname) ? $deviceDetail->hostname : null,
                'unit' => isset($deviceDetail->unit_name) ? $deviceDetail->unit_name : null,
                'ip_address' => $deviceDetail->ip_address,
                'device_brand' => isset($deviceDetail->brand) ? $deviceDetail->brand : null,
                'device_model' => isset($deviceDetail->model) ? $deviceDetail->model : null,
                'users' => $users
            ];
        }
        else {
            $response['device_detected'] = false;
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response));
    }

    public function show($id)
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method === 'GET') {
            $ticket = $this->Ticket_model->getTicketDetail($id);
            if (!$ticket) {
                return $this->errorResponse('Ticket not found', 404);
            }

            $attachments = $this->TicketAttachment_model->getByTicketId($id);

            $data = [
                'ticket' => $ticket,
                'attachments' => $attachments
            ];

            return $this->successResponse($data, 'Ticket details retrieved');
        }
        elseif ($method === 'PUT') {
            return $this->updateTicketDetails($id);
        }
        elseif ($method === 'DELETE') {
            return $this->deleteTicket($id);
        }
        else {
            return $this->errorResponse('Method Not Allowed', 405);
        }
    }

    private function deleteTicket($id)
    {
        $ticket = $this->Ticket_model->getTicketDetail($id);
        if (!$ticket) {
            return $this->errorResponse('Ticket not found', 404);
        }

                $attachments = $this->TicketAttachment_model->getByTicketId($id);
        if ($attachments) {
            foreach ($attachments as $att) {
                $file_path = FCPATH . ltrim($att->file_path, '/');
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }
            $this->TicketAttachment_model->deleteByTicketId($id);
        }

        $this->Ticket_model->delete($id);

        $this->triggerWebSocket('ticket_deleted', ['ticket_id' => $id]);

        return $this->successResponse(null, 'Ticket berhasil dihapus');
    }

    private function updateTicketDetails($id)
    {
        // Cek role admin melalui Header request (mencontoh Auth dari AdminUsers)
        $role = $this->input->get_request_header('X-User-Role', TRUE);
        if (empty($role) || !in_array(strtolower((string)$role), ['admin', 'superadmin'])) {
            return $this->errorResponse('Access denied. Admin only.', 403);
        }

        $stream = $this->input->raw_input_stream;
        $putData = json_decode($stream, true);

        if (!$putData) {
            return $this->errorResponse('Invalid or empty JSON body', 400);
        }

        $ticket = $this->Ticket_model->getTicketDetail($id);
        if (!$ticket) {
            return $this->errorResponse('Ticket not found', 404);
        }

        $updateData = [];
        // Daftar field yang diperbolehkan untuk diupdate
        $editable_fields = [
            'device_id', 'reporter_name', 'reporter_unit', 'reporter_contact',
            'category_id', 'subcategory_id', 'title', 'description', 'handled_by'
        ];

        foreach ($editable_fields as $field) {
            // Menggunakan array_key_exists agar nilai null/kosong dari client tetap bisa terupdate (jika diperbolehkan db)
            if (array_key_exists($field, $putData)) {
                $updateData[$field] = $putData[$field];
            }
        }

        if (empty($updateData)) {
            return $this->errorResponse('No valid fields provided to update', 400);
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');
        $this->Ticket_model->update($id, $updateData);

        $this->triggerWebSocket('ticket_updated', ['ticket_id' => $id]);

        return $this->successResponse(null, 'Data ticket berhasil diupdate');
    }

    public function updateStatus($id)
    {
        if ($this->input->server('REQUEST_METHOD') !== 'PUT') {
            return $this->errorResponse('Method Not Allowed', 405);
        }

        $stream = $this->input->raw_input_stream;
        $putData = json_decode($stream, true);

        if (!$putData || !isset($putData['status'])) {
            return $this->errorResponse('Status is required', 400);
        }

        $status = $putData['status'];
        
        $status_map = [
            'baru' => 'open',
            'open' => 'open',
            'proses' => 'process',
            'diproses' => 'process',
            'process' => 'process',
            'selesai' => 'done',
            'done' => 'done',
            'on hold' => 'on_hold',
            'on_hold' => 'on_hold',
            'canceled' => 'cancelled',
            'cancelled' => 'cancelled',
            'batal' => 'cancelled',
            'pending' => 'pending'
        ];

        if (!array_key_exists($status, $status_map)) {
            return $this->errorResponse('Invalid status value', 400);
        }

        $db_status = $status_map[$status];

        $ticket = $this->Ticket_model->getTicketDetail($id);
        if (!$ticket) {
            return $this->errorResponse('Ticket not found', 404);
        }

        $update_data = [
            'status' => $db_status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (($db_status === 'process') && empty($ticket->first_response_at)) {
            $update_data['first_response_at'] = date('Y-m-d H:i:s');
        }

        $this->Ticket_model->update($id, $update_data);

        $this->triggerWebSocket('status_updated', ['ticket_id' => $id, 'new_status' => $db_status]);

        return $this->successResponse(null, 'Status ticket berhasil diupdate');
    }

    private function triggerWebSocket($eventName, $data = null)
    {
        $url = 'http://localhost:3001/api/webhook/ticket';
        
        $payload = json_encode([
            'event' => $eventName,
            'data' => $data
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ]);
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        
        curl_exec($ch);
        curl_close($ch);
    }
}



