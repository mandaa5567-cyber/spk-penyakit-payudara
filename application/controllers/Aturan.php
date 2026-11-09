<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aturan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak: Hanya Administrator yang berhak mengelola basis aturan.');
            redirect('dashboard');
        }
        $this->load->model('Aturan_model');
    }

    public function index() {
        $data['title'] = 'Manajemen Basis Aturan & Faktor Risiko';
        $data['active_menu'] = 'aturan';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $data['faktor_list'] = $this->Aturan_model->get_all_faktor();
        $data['kategori_list'] = $this->Aturan_model->get_all_kategori();
        $data['rules_list'] = $this->Aturan_model->get_all_rules();
        $data['kebutuhan_list'] = $this->Aturan_model->get_kebutuhan_sistem();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('aturan/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function update_faktor() {
        $id_faktor = $this->input->post('id_faktor');
        $nama_faktor = trim($this->input->post('nama_faktor'));
        $tipe_input = $this->input->post('tipe_input');
        $operator = $this->input->post('operator');
        $ambang = $this->input->post('ambang');
        $nilai_pemicu = trim($this->input->post('nilai_pemicu'));
        $bobot = (int)$this->input->post('bobot');
        $override_tinggi = $this->input->post('override_tinggi') ? 1 : 0;

        $update_data = [
            'nama_faktor'     => $nama_faktor,
            'tipe_input'      => $tipe_input,
            'operator'        => !empty($operator) ? $operator : null,
            'ambang'          => ($ambang !== '') ? (float)$ambang : null,
            'nilai_pemicu'    => !empty($nilai_pemicu) ? $nilai_pemicu : null,
            'bobot'           => $bobot,
            'override_tinggi' => $override_tinggi
        ];

        $this->Aturan_model->update_faktor($id_faktor, $update_data);
        $this->session->set_flashdata('success', "Faktor risiko $id_faktor berhasil diperbarui.");
        redirect('aturan');
    }

    public function update_kategori() {
        $id_kategori = $this->input->post('id_kategori');
        $skor_min = (int)$this->input->post('skor_min');
        $rekomendasi = trim($this->input->post('rekomendasi'));

        $this->Aturan_model->update_kategori($id_kategori, [
            'skor_min'    => $skor_min,
            'rekomendasi' => $rekomendasi
        ]);

        $this->session->set_flashdata('success', 'Ambang batas kategori & rekomendasi berhasil diperbarui.');
        redirect('aturan');
    }
}
