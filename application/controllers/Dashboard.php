<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->model('Skrining_model');
        $this->load->model('Aturan_model');
        $this->load->model('Pasien_model');
    }

    public function index() {
        $data['title'] = 'Dashboard Skrining Risiko Kanker Payudara';
        $data['active_menu'] = 'dashboard';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $data['stats'] = $this->Skrining_model->get_stats();
        $data['recent_screenings'] = $this->Skrining_model->get_all_hasil(5);
        $data['rekap_kategori'] = $this->Aturan_model->get_rekap_kategori();
        $data['frekuensi_faktor'] = $this->Aturan_model->get_frekuensi_faktor();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('layout/footer', $data);
    }
}
