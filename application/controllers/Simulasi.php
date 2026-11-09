<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Simulasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->model('Skrining_model');
        $this->load->model('Pasien_model');
        $this->load->model('Aturan_model');
    }

    public function index() {
        $data['title'] = 'Simulasi Skrining Risiko Kanker Payudara (CDSS)';
        $data['active_menu'] = 'simulasi';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $data['pasien_list'] = $this->Pasien_model->get_all();
        $data['faktor_list'] = $this->Aturan_model->get_all_faktor();
        $data['kategori_list'] = $this->Aturan_model->get_all_kategori();
        $data['rules_list'] = $this->Aturan_model->get_all_rules();

        // Preset data dari Excel Sheet 4 & 5
        $data['presets'] = [
            'contoh_1' => [
                'nama_kasus' => 'Contoh 1 (Excel: P-001 - Risiko Sedang, Skor 7)',
                'usia' => 52,
                'menarche' => 11,
                'status_menopause' => 'Belum menopause',
                'usia_menopause' => '',
                'bb' => 68.0,
                'tb' => 155.0,
                'kerabat' => 'Ibu',
                'alkohol' => 'Tidak pernah',
                'aktivitas' => 'Kurang',
                'densitas' => 'B',
                'ca_pribadi' => false,
                'brca' => false,
                'nulipara' => false,
                'tidak_menyusui' => false,
                'kb_tsh' => false,
                'biopsi_atipikal' => false,
                'radioterapi' => false
            ],
            'contoh_2' => [
                'nama_kasus' => 'Contoh 2 (Excel: P-002 - Risiko Tinggi, Skor 15 via BRCA Override)',
                'usia' => 58,
                'menarche' => 12,
                'status_menopause' => 'Sudah menopause',
                'usia_menopause' => 56,
                'bb' => 78.0,
                'tb' => 152.0,
                'kerabat' => 'Saudari kandung',
                'alkohol' => 'Kadang-kadang',
                'aktivitas' => 'Cukup',
                'densitas' => 'D',
                'ca_pribadi' => false,
                'brca' => true,
                'nulipara' => false,
                'tidak_menyusui' => true,
                'kb_tsh' => false,
                'biopsi_atipikal' => false,
                'radioterapi' => false
            ],
            'contoh_3' => [
                'nama_kasus' => 'Contoh 3 (Pasien Muda Sehat - Risiko Rendah, Skor 0)',
                'usia' => 28,
                'menarche' => 13,
                'status_menopause' => 'Belum menopause',
                'usia_menopause' => '',
                'bb' => 52.0,
                'tb' => 160.0,
                'kerabat' => 'Tidak ada',
                'alkohol' => 'Tidak pernah',
                'aktivitas' => 'Cukup',
                'densitas' => 'A',
                'ca_pribadi' => false,
                'brca' => false,
                'nulipara' => false,
                'tidak_menyusui' => false,
                'kb_tsh' => false,
                'biopsi_atipikal' => false,
                'radioterapi' => false
            ],
            'contoh_4' => [
                'nama_kasus' => 'Contoh 4 (Kasus Override Radioterapi Dada - Risiko Tinggi)',
                'usia' => 45,
                'menarche' => 13,
                'status_menopause' => 'Belum menopause',
                'usia_menopause' => '',
                'bb' => 64.0,
                'tb' => 158.0,
                'kerabat' => 'Tidak ada',
                'alkohol' => 'Tidak pernah',
                'aktivitas' => 'Cukup',
                'densitas' => 'B',
                'ca_pribadi' => false,
                'brca' => false,
                'nulipara' => false,
                'tidak_menyusui' => false,
                'kb_tsh' => false,
                'biopsi_atipikal' => false,
                'radioterapi' => true
            ]
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('simulasi/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function transfer_ke_pasien() {
        $id_pasien = $this->input->post('id_pasien');
        $id_user = $this->session->userdata('id_user');

        if (empty($id_pasien) && $this->input->post('new_patient_nama')) {
            $pasien_data = [
                'nama'          => trim($this->input->post('new_patient_nama')),
                'no_rm'         => trim($this->input->post('new_patient_no_rm')),
                'tanggal_lahir' => $this->input->post('new_patient_dob'),
                'alamat'        => trim($this->input->post('new_patient_alamat'))
            ];
            $id_pasien = $this->Pasien_model->insert($pasien_data);
        }

        if (empty($id_pasien)) {
            $this->session->set_flashdata('error', 'Silakan pilih pasien terdaftar atau isi nama pasien baru untuk menyimpan hasil simulasi.');
            redirect('simulasi');
        }

        $header_data = [
            'tanggal'          => date('Y-m-d H:i:s'),
            'status_menopause' => $this->input->post('status_menopause') ?? 'Belum menopause',
            'berat_badan'      => (float)$this->input->post('berat_badan'),
            'tinggi_badan'     => (float)$this->input->post('tinggi_badan')
        ];

        // Gather 15 factors
        $faktor_inputs = [];
        $all_faktor = $this->Aturan_model->get_all_faktor();
        foreach ($all_faktor as $f) {
            $fid = $f['id_faktor'];
            $val = $this->input->post('f_' . $fid);

            if ($f['tipe_input'] === 'pilihan' && $f['nilai_pemicu'] === 'Ya') {
                $val = ($this->input->post('f_' . $fid) === 'Ya') ? 'Ya' : 'Tidak';
            }

            if ($fid === 'F06') {
                if ($header_data['status_menopause'] === 'Sudah menopause') {
                    $val = $this->input->post('usia_menopause');
                } else {
                    $val = '';
                }
            }

            if ($fid === 'F01' && empty($val)) {
                $val = $this->input->post('usia_input');
            }

            $faktor_inputs[$fid] = $val;
        }

        $id_skrining = $this->Skrining_model->simpan_skrining($id_pasien, $id_user, $header_data, $faktor_inputs);

        $this->session->set_flashdata('success', 'Hasil simulasi skrining berhasil ditransfer dan disimpan secara permanen ke rekam medis pasien.');
        redirect('skrining/hasil/' . $id_skrining);
    }
}
