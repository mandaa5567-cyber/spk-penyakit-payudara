<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skrining extends CI_Controller {

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
        redirect('skrining/riwayat');
    }

    public function baru($id_pasien = null) {
        $data['title'] = 'Skrining Risiko Baru - CDSS Kanker Payudara';
        $data['active_menu'] = 'skrining_baru';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $data['selected_pasien'] = null;
        if (!empty($id_pasien)) {
            $data['selected_pasien'] = $this->Pasien_model->get_by_id($id_pasien);
        }

        $data['pasien_list'] = $this->Pasien_model->get_all();
        $data['faktor_list'] = $this->Aturan_model->get_all_faktor();
        $data['kategori_list'] = $this->Aturan_model->get_all_kategori();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('skrining/form', $data);
        $this->load->view('layout/footer', $data);
    }

    public function simpan() {
        $id_pasien = $this->input->post('id_pasien');
        $id_user = $this->session->userdata('id_user');

        // Check if creating new patient on the fly
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
            $this->session->set_flashdata('error', 'Pasien wajib dipilih atau diisi terlebih dahulu.');
            redirect('skrining/baru');
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

            // Handle checkboxes
            if ($f['tipe_input'] === 'pilihan' && $f['nilai_pemicu'] === 'Ya') {
                $val = ($this->input->post('f_' . $fid) === 'Ya') ? 'Ya' : 'Tidak';
            }

            // Handle menopause age logic
            if ($fid === 'F06') {
                if ($header_data['status_menopause'] === 'Sudah menopause') {
                    $val = $this->input->post('usia_menopause');
                } else {
                    $val = '';
                }
            }

            // Handle age factor F01
            if ($fid === 'F01' && empty($val)) {
                $val = $this->input->post('usia_input');
            }

            $faktor_inputs[$fid] = $val;
        }

        $id_skrining = $this->Skrining_model->simpan_skrining($id_pasien, $id_user, $header_data, $faktor_inputs);

        $this->session->set_flashdata('success', 'Skrining berhasil diproses dengan metode Rule-Based Reasoning.');
        redirect('skrining/hasil/' . $id_skrining);
    }

    public function hasil($id_skrining) {
        $skrining = $this->Skrining_model->get_by_id($id_skrining);
        if (!$skrining) {
            $this->session->set_flashdata('error', 'Data skrining tidak ditemukan.');
            redirect('skrining/riwayat');
        }

        $data['title'] = 'Hasil Skrining - ' . $skrining['nama_pasien'];
        $data['active_menu'] = 'skrining_riwayat';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];
        $data['skrining'] = $skrining;

        // Calculate age
        $data['usia'] = null;
        if (!empty($skrining['tanggal_lahir'])) {
            $dob = new DateTime($skrining['tanggal_lahir']);
            $now = new DateTime($skrining['tanggal']);
            $data['usia'] = $now->diff($dob)->y;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('skrining/hasil', $data);
        $this->load->view('layout/footer', $data);
    }

    public function cetak($id_skrining) {
        $skrining = $this->Skrining_model->get_by_id($id_skrining);
        if (!$skrining) {
            $this->session->set_flashdata('error', 'Data skrining tidak ditemukan.');
            redirect('skrining/riwayat');
        }

        $data['title'] = 'Cetak Lembar Skrining - ' . $skrining['no_rm'];
        $data['skrining'] = $skrining;

        // Calculate age
        $data['usia'] = null;
        if (!empty($skrining['tanggal_lahir'])) {
            $dob = new DateTime($skrining['tanggal_lahir']);
            $now = new DateTime($skrining['tanggal']);
            $data['usia'] = $now->diff($dob)->y;
        }

        $this->load->view('skrining/cetak', $data);
    }

    public function riwayat() {
        $data['title'] = 'Riwayat Skrining Risiko Kanker Payudara';
        $data['active_menu'] = 'skrining_riwayat';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $filter_kategori = $this->input->get('kategori');
        $search = $this->input->get('q');

        $data['filter_kategori'] = $filter_kategori;
        $data['search'] = $search;
        $data['riwayat_list'] = $this->Skrining_model->get_all_hasil(null, $filter_kategori, $search);
        $data['stats'] = $this->Skrining_model->get_stats();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('skrining/riwayat', $data);
        $this->load->view('layout/footer', $data);
    }

    public function hapus($id_skrining) {
        $this->Skrining_model->delete_skrining($id_skrining);
        $this->session->set_flashdata('success', 'Data skrining berhasil dihapus.');
        redirect('skrining/riwayat');
    }
}
