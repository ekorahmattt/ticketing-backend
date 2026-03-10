<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Tickets extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ticket_model');
    }

    public function index()
    {
        $tickets = $this->Ticket_model->getAllTickets();
        $this->successResponse($tickets, 'Daftar ticket berhasil dimuat');
    }
}
