<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket_model extends CI_Model
{

    protected $table = 'tickets';

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
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    // Specific methods for Ticket_model

    public function getAllTickets()
    {
        $this->db->select('tickets.id, tickets.reporter_name, tickets.reporter_unit, categories.name as category, subcategories.name as subcategory, tickets.title, tickets.status, tickets.created_at, users.name as teknisi');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = tickets.category_id', 'left');
        $this->db->join('subcategories', 'subcategories.id = tickets.subcategory_id', 'left');
        $this->db->join('users', 'users.id = tickets.handled_by', 'left');
        $this->db->order_by('tickets.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function getTicketsByDeviceId($device_id)
    {
        $this->db->select('tickets.id, tickets.reporter_name, tickets.reporter_unit, categories.name as category, subcategories.name as subcategory, tickets.title, tickets.status, tickets.created_at, users.name as teknisi');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = tickets.category_id', 'left');
        $this->db->join('subcategories', 'subcategories.id = tickets.subcategory_id', 'left');
        $this->db->join('users', 'users.id = tickets.handled_by', 'left');
        $this->db->where('tickets.device_id', $device_id);
        $this->db->order_by('tickets.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function getTicketDetail($id)
    {
        $this->db->select('tickets.*, devices.device_name, devices.ip_address, categories.name as category, subcategories.name as subcategory, users.name as admin_name');
        $this->db->from($this->table);
        $this->db->join('devices', 'devices.id = tickets.device_id', 'left');
        $this->db->join('categories', 'categories.id = tickets.category_id', 'left');
        $this->db->join('subcategories', 'subcategories.id = tickets.subcategory_id', 'left');
        $this->db->join('users', 'users.id = tickets.handled_by', 'left');
        $this->db->where('tickets.id', $id);
        return $this->db->get()->row();
    }

    public function updateStatus($id, $status)
    {
        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function assignAdmin($ticket_id, $admin_id)
    {
        $data = array(
            'handled_by' => $admin_id,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $ticket_id);
        return $this->db->update($this->table, $data);
    }
}
