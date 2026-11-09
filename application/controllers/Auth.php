<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->login();
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        $data['title'] = 'Login Petugas - CDSS Kanker Payudara';
        $data['error'] = '';

        if ($this->input->post()) {
            $username = trim($this->input->post('username'));
            $password = trim($this->input->post('password'));

            $user = $this->User_model->verify_login($username, $password);
            if ($user) {
                $this->session->set_userdata([
                    'id_user'   => $user['id_user'],
                    'nama'      => $user['nama'],
                    'username'  => $user['username'],
                    'role'      => $user['role'],
                    'logged_in' => true
                ]);
                $this->session->set_flashdata('success', 'Selamat datang kembali, ' . $user['nama']);
                redirect('dashboard');
            } else {
                $data['error'] = 'Username atau kata sandi tidak valid.';
            }
        }

        $this->load->view('auth/login', $data);
    }

    public function quick_login($role = 'admin') {
        $username = ($role === 'nakes') ? 'nakes' : 'admin';
        $user = $this->User_model->get_by_username($username);

        if ($user) {
            $this->session->set_userdata([
                'id_user'   => $user['id_user'],
                'nama'      => $user['nama'],
                'username'  => $user['username'],
                'role'      => $user['role'],
                'logged_in' => true
            ]);
            $this->session->set_flashdata('success', 'Masuk sebagai ' . $user['nama'] . ' (' . ucfirst($user['role']) . ')');
        }
        redirect('dashboard');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
