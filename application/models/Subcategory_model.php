<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subcategory_model extends CI_Model
{
    protected $table = 'subcategories';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua subcategory beserta nama category-nya.
     */
    public function getAll()
    {
        return $this->db
            ->select('s.id, s.category_id, s.name, s.sla_minutes, c.name AS category_name')
            ->from('subcategories s')
            ->join('categories c', 'c.id = s.category_id', 'left')
            ->order_by('c.name', 'ASC')
            ->order_by('s.name', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Ambil subcategory by ID beserta nama category-nya.
     */
    public function getById($id)
    {
        return $this->db
            ->select('s.id, s.category_id, s.name, s.sla_minutes, c.name AS category_name')
            ->from('subcategories s')
            ->join('categories c', 'c.id = s.category_id', 'left')
            ->where('s.id', $id)
            ->get()
            ->row();
    }

    /**
     * Tambah subcategory baru.
     */
    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update subcategory.
     */
    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Hapus subcategory.
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Cek apakah subcategory dipakai di tabel tickets.
     */
    public function isUsedInTickets($id)
    {
        return $this->db->where('subcategory_id', $id)->count_all_results('tickets') > 0;
    }
}
