<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function count_all() {
        return $this->db->count_all('pasien');
    }

    public function get_all($search = null) {
        $this->db->select('p.*, 
            COUNT(s.id_skrining) as total_skrining,
            MAX(s.tanggal) as skrining_terakhir,
            (SELECT k.nama_kategori FROM skrining s2 
             LEFT JOIN kategori_risiko k ON k.id_kategori = s2.id_kategori 
             WHERE s2.id_pasien = p.id_pasien 
             ORDER BY s2.tanggal DESC LIMIT 1) as kategori_terakhir');
        $this->db->from('pasien p');
        $this->db->join('skrining s', 's.id_pasien = p.id_pasien', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('p.no_rm', $search);
            $this->db->or_like('p.alamat', $search);
            $this->db->group_end();
        }

        $this->db->group_by('p.id_pasien');
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id_pasien) {
        return $this->db->get_where('pasien', ['id_pasien' => $id_pasien])->row_array();
    }

    public function get_by_no_rm($no_rm) {
        return $this->db->get_where('pasien', ['no_rm' => $no_rm])->row_array();
    }

    public function generate_no_rm() {
        $year = date('Y');
        $prefix = "RM-{$year}-";
        $this->db->like('no_rm', $prefix, 'after');
        $this->db->order_by('id_pasien', 'DESC');
        $last = $this->db->get('pasien', 1)->row_array();

        if ($last) {
            $last_no = substr($last['no_rm'], -3);
            $next_no = str_pad((int)$last_no + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next_no = '001';
        }
        return $prefix . $next_no;
    }

    public function insert($data) {
        if (empty($data['no_rm'])) {
            $data['no_rm'] = $this->generate_no_rm();
        }
        $this->db->insert('pasien', $data);
        return $this->db->insert_id();
    }

    public function update($id_pasien, $data) {
        return $this->db->where('id_pasien', $id_pasien)->update('pasien', $data);
    }

    public function delete($id_pasien) {
        // Skrining will cascade if configured or delete cascade manually
        $this->db->where('id_pasien', $id_pasien)->delete('pasien');
        return $this->db->affected_rows() > 0;
    }

    public function get_history($id_pasien) {
        $this->db->select('s.*, k.nama_kategori, k.rekomendasi, u.nama as nama_petugas');
        $this->db->from('skrining s');
        $this->db->join('kategori_risiko k', 'k.id_kategori = s.id_kategori', 'left');
        $this->db->join('users u', 'u.id_user = s.id_user', 'left');
        $this->db->where('s.id_pasien', $id_pasien);
        $this->db->order_by('s.tanggal', 'DESC');
        return $this->db->get()->result_array();
    }
}
