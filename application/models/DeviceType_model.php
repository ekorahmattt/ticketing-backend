<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeviceType_model extends CI_Model
{
    protected $table = 'device_types';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        return $this->db->order_by('id', 'ASC')->get($this->table)->result();
    }

    public function getById($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }
}
