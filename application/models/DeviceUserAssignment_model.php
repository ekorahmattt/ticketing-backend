<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeviceUserAssignment_model extends CI_Model
{

    protected $table = 'device_user_assignments';

    public function __construct()
    {
        parent::__construct();
    }

    public function getUsersByDevice($device_id)
    {
        $this->db->select('device_users.id, device_users.name');
        $this->db->from($this->table);
        $this->db->join('device_users', 'device_users.id = ' . $this->table . '.user_id', 'left');
        $this->db->where($this->table . '.device_id', $device_id);
        return $this->db->get()->result();
    }

    public function assignUser($device_id, $user_id)
    {
        $data = [
            'device_id' => $device_id,
            'user_id' => $user_id
        ];
        return $this->db->insert($this->table, $data);
    }

    public function deleteByDevice($device_id)
    {
        $this->db->where('device_id', $device_id);
        return $this->db->delete($this->table);
    }
}
