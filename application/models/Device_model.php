<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Device_model extends CI_Model
{

    protected $table = 'devices';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua device, JOIN ke device_types, units, users
     */
    public function getAll()
    {
        $this->db->select('d.*, cb.name AS created_by_name, ub.name AS updated_by_name, dt.name AS device_type_name, u.name AS unit_name, u.code AS unit_code');
        $this->db->from($this->table . ' d');
        $this->db->join('users cb',        'cb.id = d.created_by',    'left');
        $this->db->join('users ub',        'ub.id = d.updated_by',    'left');
        $this->db->join('device_types dt', 'dt.id = d.device_type_id', 'left');
        $this->db->join('units u',         'u.id = d.unit_id',         'left');
        return $this->db->get()->result();
    }

    /**
     * Ambil satu device berdasarkan ID, JOIN ke device_types, units, users
     */
    public function getById($id)
    {
        $this->db->select('d.*, cb.name AS created_by_name, ub.name AS updated_by_name, dt.name AS device_type_name, u.name AS unit_name, u.code AS unit_code');
        $this->db->from($this->table . ' d');
        $this->db->join('users cb',        'cb.id = d.created_by',    'left');
        $this->db->join('users ub',        'ub.id = d.updated_by',    'left');
        $this->db->join('device_types dt', 'dt.id = d.device_type_id', 'left');
        $this->db->join('units u',         'u.id = d.unit_id',         'left');
        $this->db->where('d.id', $id);
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

    // ── Specific helpers ──────────────────────────────────────────────────────

    public function getByIp($ip)
    {
        return $this->db->where('ip_address', $ip)->get($this->table)->row();
    }

    public function updateLastSeen($device_id)
    {
        $data = ['last_seen' => date('Y-m-d H:i:s')];
        $this->db->where('id', $device_id);
        return $this->db->update($this->table, $data);
    }
}
