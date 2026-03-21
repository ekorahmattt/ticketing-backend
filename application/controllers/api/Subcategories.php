<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/BaseApiController.php';

class Subcategories extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Subcategory_model');
    }

    /**
     * GET  /api/subcategories         → daftar semua subcategory (+ nama kategori)
     * POST /api/subcategories         → tambah subcategory baru
     */
    public function index()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // ── GET ────────────────────────────────────────────────────────────────
        if ($method === 'GET') {
            $data = $this->Subcategory_model->getAll();
            return $this->successResponse($data, 'subcategories retrieved');
        }

        // ── POST ───────────────────────────────────────────────────────────────
        if ($method === 'POST') {
            $body = json_decode($this->input->raw_input_stream, true);

            if (empty($body['name'])) {
                return $this->errorResponse('Nama jenis gangguan wajib diisi.', 400);
            }
            if (empty($body['category_id'])) {
                return $this->errorResponse('Kategori wajib dipilih.', 400);
            }

            $insertData = [
                'category_id'  => (int) $body['category_id'],
                'name'         => trim($body['name']),
                'sla_minutes'  => isset($body['sla_minutes']) && $body['sla_minutes'] !== '' ? (int) $body['sla_minutes'] : null,
            ];

            $id = $this->Subcategory_model->create($insertData);
            if ($id) {
                $created = $this->Subcategory_model->getById($id);
                return $this->successResponse($created, 'subcategory created');
            }

            return $this->errorResponse('Gagal menambahkan jenis gangguan.', 500);
        }

        return $this->errorResponse('Method Not Allowed', 405);
    }

    /**
     * GET    /api/subcategories/{id}  → detail
     * PUT    /api/subcategories/{id}  → update
     * DELETE /api/subcategories/{id}  → hapus
     */
    public function show($id)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // ── GET ────────────────────────────────────────────────────────────────
        if ($method === 'GET') {
            $row = $this->Subcategory_model->getById($id);
            if (!$row) return $this->errorResponse('Jenis gangguan tidak ditemukan.', 404);
            return $this->successResponse($row, 'subcategory detail');
        }

        // ── PUT ────────────────────────────────────────────────────────────────
        if ($method === 'PUT') {
            $row = $this->Subcategory_model->getById($id);
            if (!$row) return $this->errorResponse('Jenis gangguan tidak ditemukan.', 404);

            $body = json_decode($this->input->raw_input_stream, true);

            if (empty($body['name'])) {
                return $this->errorResponse('Nama jenis gangguan wajib diisi.', 400);
            }
            if (empty($body['category_id'])) {
                return $this->errorResponse('Kategori wajib dipilih.', 400);
            }

            $updateData = [
                'category_id'  => (int) $body['category_id'],
                'name'         => trim($body['name']),
                'sla_minutes'  => isset($body['sla_minutes']) && $body['sla_minutes'] !== '' ? (int) $body['sla_minutes'] : null,
            ];

            $this->Subcategory_model->update($id, $updateData);
            $updated = $this->Subcategory_model->getById($id);
            return $this->successResponse($updated, 'subcategory updated');
        }

        // ── DELETE ─────────────────────────────────────────────────────────────
        if ($method === 'DELETE') {
            $row = $this->Subcategory_model->getById($id);
            if (!$row) return $this->errorResponse('Jenis gangguan tidak ditemukan.', 404);

            if ($this->Subcategory_model->isUsedInTickets($id)) {
                return $this->errorResponse('Jenis gangguan tidak dapat dihapus karena sudah digunakan pada tiket.', 409);
            }

            if ($this->Subcategory_model->delete($id)) {
                return $this->successResponse((object)[], 'subcategory deleted');
            }

            return $this->errorResponse('Gagal menghapus jenis gangguan.', 500);
        }

        return $this->errorResponse('Method Not Allowed', 405);
    }
}
