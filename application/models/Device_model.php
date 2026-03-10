<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Device_model extends CI_Model
{

    protected $table = 'devices';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        // Can optionally join device_types if needed, using simple get for now
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

    // Specific methods for Device_model

    public function getByIp($ip)
    {
        return $this->db->where('ip_address', $ip)->get($this->table)->row();
    }

    public function updateLastSeen($device_id)
    {
        $data = array(
            'last_seen' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $device_id);
        return $this->db->update($this->table, $data);
    }
}
