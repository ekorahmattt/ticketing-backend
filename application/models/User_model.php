<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        return $this->db->get($this->table)->result();
    }

    public function getAllAdmins()
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

    public function getByUsername($username)
    {
        return $this->db->where('username', $username)->get($this->table)->row();
    }

    public function updateLastLogin($id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, array('last_login' => date('Y-m-d H:i:s')));
    }
}
