<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeviceConnection_model extends CI_Model
{

    protected $table = 'device_connections';

    public function __construct()
    {
        parent::__construct();
    }

    public function getConnections($device_id)
    {
        $this->db->select($this->table . '.child_device_id as device_id, devices.device_name');
        $this->db->from($this->table);
        $this->db->join('devices', 'devices.id = ' . $this->table . '.child_device_id', 'left');
        $this->db->where($this->table . '.parent_device_id', $device_id);
        return $this->db->get()->result();
    }

    /**
     * Ambil daftar parent devices (e.g. komputer) yang terhubung ke device ini sebagai child.
     * Digunakan untuk pre-populate dropdown "Perangkat yang Terhubung" saat edit.
     */
    public function getParentDevices($device_id)
    {
        $this->db->select($this->table . '.parent_device_id as device_id, devices.device_name, ' . $this->table . '.connection_type');
        $this->db->from($this->table);
        $this->db->join('devices', 'devices.id = ' . $this->table . '.parent_device_id', 'left');
        $this->db->where($this->table . '.child_device_id', $device_id);
        return $this->db->get()->result();
    }

    public function createConnection($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function deleteByDevice($device_id)
    {
        $this->db->where('parent_device_id', $device_id);
        return $this->db->delete($this->table);
    }

    /**
     * Hapus semua koneksi di mana device ini adalah parent ATAU child.
     * Digunakan saat update/delete agar tidak ada koneksi orphan.
     */
    public function deleteAllByDevice($device_id)
    {
        $this->db->where('parent_device_id', $device_id)
                 ->or_where('child_device_id', $device_id)
                 ->delete($this->table);
    }
}
