<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TicketAttachment_model extends CI_Model
{
    protected $table = 'ticket_attachments';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function getByTicketId($ticket_id)
    {
        $this->db->where('ticket_id', $ticket_id);
        return $this->db->get($this->table)->result();
    }

    public function deleteByTicketId($ticket_id)
    {
        $this->db->where('ticket_id', $ticket_id);
        return $this->db->delete($this->table);
    }
}
