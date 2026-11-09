<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->model('Pasien_model');
    }

    public function index() {
        $data['title'] = 'Data Pasien - CDSS Kanker Payudara';
        $data['active_menu'] = 'pasien';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $search = $this->input->get('q');
        $data['search'] = $search;
        $data['pasien_list'] = $this->Pasien_model->get_all($search);
        $data['next_no_rm'] = $this->Pasien_model->generate_no_rm();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('pasien/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function simpan() {
        $id_pasien = $this->input->post('id_pasien');
        $no_rm = trim($this->input->post('no_rm'));
        $nama = trim($this->input->post('nama'));
        $tanggal_lahir = $this->input->post('tanggal_lahir');
        $alamat = trim($this->input->post('alamat'));

        if (empty($nama)) {
            $this->session->set_flashdata('error', 'Nama pasien wajib diisi.');
            redirect('pasien');
        }

        $data = [
            'nama'          => $nama,
            'tanggal_lahir' => !empty($tanggal_lahir) ? $tanggal_lahir : null,
            'alamat'        => $alamat
        ];

        if (!empty($no_rm)) {
            $data['no_rm'] = $no_rm;
        }

        if (!empty($id_pasien)) {
            $this->Pasien_model->update($id_pasien, $data);
            $this->session->set_flashdata('success', 'Data pasien berhasil diperbarui.');
        } else {
            $new_id = $this->Pasien_model->insert($data);
            $this->session->set_flashdata('success', 'Pasien baru berhasil ditambahkan.');
            if ($this->input->post('start_screening')) {
                redirect('skrining/baru/' . $new_id);
            }
        }

        redirect('pasien');
    }

    public function detail($id_pasien) {
        $pasien = $this->Pasien_model->get_by_id($id_pasien);
        if (!$pasien) {
            $this->session->set_flashdata('error', 'Pasien tidak ditemukan.');
            redirect('pasien');
        }

        $data['title'] = 'Profil Pasien - ' . $pasien['nama'];
        $data['active_menu'] = 'pasien';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];
        $data['pasien'] = $pasien;
        $data['riwayat'] = $this->Pasien_model->get_history($id_pasien);

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('pasien/detail', $data);
        $this->load->view('layout/footer', $data);
    }

    public function hapus($id_pasien) {
        $pasien = $this->Pasien_model->get_by_id($id_pasien);
        if ($pasien) {
            $this->Pasien_model->delete($id_pasien);
            $this->session->set_flashdata('success', 'Data pasien ' . $pasien['nama'] . ' berhasil dihapus.');
        }
        redirect('pasien');
    }

    public function ajax_search() {
        $q = $this->input->get('q');
        $patients = $this->Pasien_model->get_all($q);
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($patients));
    }
}
