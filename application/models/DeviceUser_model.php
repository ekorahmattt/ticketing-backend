<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeviceUser_model extends CI_Model
{

    protected $table = 'device_users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua device_users, JOIN ke units untuk dapat nama unit
     */
    public function getAll()
    {
        $this->db->select('du.*, u.name AS unit_name, u.code AS unit_code');
        $this->db->from($this->table . ' du');
        $this->db->join('units u', 'u.id = du.unit_id', 'left');
        $this->db->order_by('du.name', 'ASC');
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->select('du.*, u.name AS unit_name, u.code AS unit_code');
        $this->db->from($this->table . ' du');
        $this->db->join('units u', 'u.id = du.unit_id', 'left');
        $this->db->where('du.id', $id);
        return $this->db->get()->row();
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
}
