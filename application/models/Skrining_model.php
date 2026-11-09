<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skrining_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('Aturan_model');
    }

    public function get_stats() {
        $total_skrining = $this->db->count_all('skrining');
        $total_pasien = $this->db->count_all('pasien');

        $this->db->select('
            COUNT(id_skrining) as total,
            AVG(total_skor) as avg_skor,
            SUM(CASE WHEN id_kategori = 3 THEN 1 ELSE 0 END) as total_tinggi,
            SUM(CASE WHEN id_kategori = 2 THEN 1 ELSE 0 END) as total_sedang,
            SUM(CASE WHEN id_kategori = 1 THEN 1 ELSE 0 END) as total_rendah
        ');
        $row = $this->db->get('skrining')->row_array();

        return [
            'total_skrining' => $total_skrining,
            'total_pasien'   => $total_pasien,
            'total_tinggi'   => (int)($row['total_tinggi'] ?? 0),
            'total_sedang'   => (int)($row['total_sedang'] ?? 0),
            'total_rendah'   => (int)($row['total_rendah'] ?? 0),
            'avg_skor'       => round((float)($row['avg_skor'] ?? 0), 1)
        ];
    }

    public function get_all_hasil($limit = null, $filter_kategori = null, $search = null) {
        $this->db->select('s.*, p.no_rm, p.nama as nama_pasien, p.tanggal_lahir, p.alamat, 
                           k.nama_kategori, k.rekomendasi, u.nama as nama_petugas');
        $this->db->from('skrining s');
        $this->db->join('pasien p', 'p.id_pasien = s.id_pasien');
        $this->db->join('users u', 'u.id_user = s.id_user', 'left');
        $this->db->join('kategori_risiko k', 'k.id_kategori = s.id_kategori', 'left');

        if (!empty($filter_kategori)) {
            $this->db->where('k.nama_kategori', $filter_kategori);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('p.no_rm', $search);
            $this->db->group_end();
        }

        $this->db->order_by('s.tanggal', 'DESC');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result_array();
    }

    public function get_by_id($id_skrining) {
        $this->db->select('s.*, p.no_rm, p.nama as nama_pasien, p.tanggal_lahir, p.alamat,
                           k.nama_kategori, k.rekomendasi, u.nama as nama_petugas');
        $this->db->from('skrining s');
        $this->db->join('pasien p', 'p.id_pasien = s.id_pasien');
        $this->db->join('users u', 'u.id_user = s.id_user', 'left');
        $this->db->join('kategori_risiko k', 'k.id_kategori = s.id_kategori', 'left');
        $this->db->where('s.id_skrining', $id_skrining);
        $header = $this->db->get()->row_array();

        if (!$header) {
            return null;
        }

        // Get details
        $this->db->select('d.*, f.nama_faktor, f.tipe_input, f.operator, f.ambang, f.nilai_pemicu, f.bobot, f.override_tinggi');
        $this->db->from('detail_skrining d');
        $this->db->join('faktor_risiko f', 'f.id_faktor = d.id_faktor');
        $this->db->where('d.id_skrining', $id_skrining);
        $this->db->order_by('d.id_faktor', 'ASC');
        $header['details'] = $this->db->get()->result_array();

        return $header;
    }

    public function simpan_skrining($id_pasien, $id_user, $header_data, $faktor_inputs) {
        // Calculate IMT if not provided
        $bb = (float)($header_data['berat_badan'] ?? 0);
        $tb = (float)($header_data['tinggi_badan'] ?? 0);
        $imt = null;
        if ($tb > 0) {
            $imt = round($bb / pow($tb / 100, 2), 1);
        }

        $insert_header = [
            'id_pasien'        => $id_pasien,
            'id_user'          => $id_user,
            'tanggal'          => $header_data['tanggal'] ?? date('Y-m-d H:i:s'),
            'status_menopause' => $header_data['status_menopause'] ?? 'Belum menopause',
            'berat_badan'      => $bb,
            'tinggi_badan'     => $tb
        ];

        $this->db->insert('skrining', $insert_header);
        $id_skrining = $this->db->insert_id();

        // Get all factors definition from DB
        $all_faktor = $this->Aturan_model->get_all_faktor();

        // Save details
        foreach ($all_faktor as $f) {
            $fid = $f['id_faktor'];
            $val = isset($faktor_inputs[$fid]) ? trim($faktor_inputs[$fid]) : '';

            // Handle special mapping: F10 Obesitas uses IMT
            if ($fid === 'F10' && ($val === '' || is_null($val))) {
                $val = (string)$imt;
            }

            $detail_data = [
                'id_skrining' => $id_skrining,
                'id_faktor'   => $fid,
                'nilai_input' => $val,
                'terpenuhi'   => 0,
                'skor'        => 0
            ];
            $this->db->insert('detail_skrining', $detail_data);
        }

        // Try calling stored procedure first
        try {
            $this->db->query("CALL sp_hitung_skrining(?)", [$id_skrining]);
        } catch (Exception $e) {
            // Fallback to PHP rule engine
            $this->evaluasi_rules_php($id_skrining);
        }

        // Also run PHP verification to guarantee consistency
        $this->evaluasi_rules_php($id_skrining);

        return $id_skrining;
    }

    public function evaluasi_rules_php($id_skrining) {
        $factors = $this->db->select('d.id_detail, d.nilai_input, f.*')
                            ->from('detail_skrining d')
                            ->join('faktor_risiko f', 'f.id_faktor = d.id_faktor')
                            ->where('d.id_skrining', $id_skrining)
                            ->get()->result_array();

        $total_skor = 0;
        $override = false;

        foreach ($factors as $item) {
            $terpenuhi = 0;
            $val = trim($item['nilai_input'] ?? '');

            if ($val !== '') {
                if ($item['tipe_input'] === 'angka') {
                    $numVal = (float)$val;
                    $ambang = (float)$item['ambang'];
                    switch ($item['operator']) {
                        case '>=': $terpenuhi = ($numVal >= $ambang) ? 1 : 0; break;
                        case '>':  $terpenuhi = ($numVal > $ambang)  ? 1 : 0; break;
                        case '<=': $terpenuhi = ($numVal <= $ambang) ? 1 : 0; break;
                        case '<':  $terpenuhi = ($numVal < $ambang)  ? 1 : 0; break;
                        case '=':  $terpenuhi = ($numVal == $ambang) ? 1 : 0; break;
                    }
                } else {
                    // Pilihan: check if val in comma-separated nilai_pemicu
                    $pemicu_arr = array_map('trim', explode(',', $item['nilai_pemicu']));
                    $terpenuhi = in_array($val, $pemicu_arr) ? 1 : 0;
                }
            }

            $skor = $terpenuhi ? (int)$item['bobot'] : 0;
            $total_skor += $skor;

            if ($terpenuhi && (int)$item['override_tinggi'] === 1) {
                $override = true;
            }

            $this->db->where('id_detail', $item['id_detail'])->update('detail_skrining', [
                'terpenuhi' => $terpenuhi,
                'skor'      => $skor
            ]);
        }

        // Determine category
        $kategori_row = null;
        if ($override) {
            $kategori_row = $this->db->get_where('kategori_risiko', ['nama_kategori' => 'Tinggi'])->row_array();
        } else {
            $kategori_row = $this->db->where('skor_min <=', $total_skor)
                                     ->order_by('skor_min', 'DESC')
                                     ->limit(1)
                                     ->get('kategori_risiko')->row_array();
        }

        $id_kategori = $kategori_row ? $kategori_row['id_kategori'] : 1;

        $this->db->where('id_skrining', $id_skrining)->update('skrining', [
            'total_skor'     => $total_skor,
            'override_aktif' => $override ? 1 : 0,
            'id_kategori'    => $id_kategori
        ]);

        return [
            'total_skor'     => $total_skor,
            'override_aktif' => $override,
            'id_kategori'    => $id_kategori
        ];
    }

    public function delete_skrining($id_skrining) {
        $this->db->where('id_skrining', $id_skrining)->delete('detail_skrining');
        $this->db->where('id_skrining', $id_skrining)->delete('skrining');
        return $this->db->affected_rows() > 0;
    }
}
