<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Unit_model extends CI_Model
{
    protected $table = 'units';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        return $this->db->order_by('name', 'ASC')->get($this->table)->result();
    }

    public function getById($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function getByName($name)
    {
        return $this->db->where('name', $name)->get($this->table)->row();
    }
}
