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
        $this->db->select('tickets.*, devices.device_name, devices.ip_address, devices.remote_address, categories.name as category, subcategories.name as subcategory, users.name as admin_name');
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

    public function getFilteredTickets($filters = [])
    {
        $this->db->select('tickets.id, tickets.created_at, categories.name as category, subcategories.name as subcategory, tickets.description, tickets.action_taken, tickets.status, users.name as teknisi, tickets.reporter_unit, tickets.reporter_name, tickets.report_hostname');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = tickets.category_id', 'left');
        $this->db->join('subcategories', 'subcategories.id = tickets.subcategory_id', 'left');
        $this->db->join('users', 'users.id = tickets.handled_by', 'left');

        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(tickets.created_at) >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(tickets.created_at) <=', $filters['end_date']);
        }
        if (!empty($filters['unit']) && $filters['unit'] !== 'Semua Unit') {
            $this->db->where('tickets.reporter_unit', $filters['unit']);
        }
        if (!empty($filters['category']) && $filters['category'] !== 'Semua Kategori') {
            $this->db->where('categories.name', $filters['category']);
        }
        if (!empty($filters['status']) && $filters['status'] !== 'Semua Status') {
            $s = strtolower($filters['status']);
            if ($s === 'open') {
                $this->db->where_in('tickets.status', ['baru', 'open']);
            } elseif ($s === 'diproses') {
                $this->db->where_in('tickets.status', ['proses', 'process', 'diproses']);
            } elseif ($s === 'selesai') {
                $this->db->where_in('tickets.status', ['selesai', 'done']);
            } elseif ($s === 'on hold') {
                $this->db->where('tickets.status', 'on_hold');
            } elseif ($s === 'canceled') {
                $this->db->where_in('tickets.status', ['canceled', 'cancelled']);
            } else {
                $this->db->where('tickets.status', $s);
            }
        }
        if (!empty($filters['search'])) {
            $q = $filters['search'];
            $this->db->group_start();
            $this->db->like('tickets.reporter_name', $q);
            $this->db->or_like('tickets.id', $q);
            $this->db->or_like('tickets.title', $q);
            $this->db->group_end();
        }

        $this->db->order_by('tickets.created_at', 'DESC');
        return $this->db->get()->result();
    }
}
