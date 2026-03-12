<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Message_model extends CI_Model
{

    protected $table = 'messages';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        return $this->db->get($this->table)->result();
    }

    public function getById($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    // Specific methods for Message_model

    public function getMessagesByTicket($ticket_id)
    {
        $this->db->select('messages.id, messages.sender_type, CASE WHEN messages.sender_type = "admin" THEN users.name WHEN messages.sender_type = "device" THEN tickets.reporter_name END AS sender_name, messages.message, messages.created_at', FALSE);
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = messages.sender_id', 'left');
        $this->db->join('tickets', 'tickets.id = messages.ticket_id', 'left');
        $this->db->where('messages.ticket_id', $ticket_id);
        $this->db->order_by('messages.created_at', 'ASC');
        return $this->db->get()->result();
    }

    public function sendMessage($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}
