<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak: Hanya Administrator yang berhak mengelola data pengguna.');
            redirect('dashboard');
        }
        $this->load->model('User_model');
    }

    public function index() {
        $data['title'] = 'Manajemen Pengguna & Hak Akses';
        $data['active_menu'] = 'users';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $data['user_list'] = $this->User_model->get_all();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('users/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function simpan() {
        $id_user = $this->input->post('id_user');
        $nama = trim($this->input->post('nama'));
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));
        $role = $this->input->post('role') ?? 'nakes';

        if (empty($nama) || empty($username)) {
            $this->session->set_flashdata('error', 'Nama dan username wajib diisi.');
            redirect('users');
        }

        $data = [
            'nama'     => $nama,
            'username' => $username,
            'role'     => $role
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        if (!empty($id_user)) {
            $this->User_model->update($id_user, $data);
            $this->session->set_flashdata('success', "Pengguna $nama berhasil diperbarui.");
        } else {
            if (empty($password)) {
                $this->session->set_flashdata('error', 'Password wajib diisi untuk pengguna baru.');
                redirect('users');
            }
            // Check username uniqueness
            if ($this->User_model->get_by_username($username)) {
                $this->session->set_flashdata('error', "Username '$username' sudah digunakan.");
                redirect('users');
            }
            $this->User_model->insert($data);
            $this->session->set_flashdata('success', "Pengguna $nama berhasil ditambahkan.");
        }

        redirect('users');
    }

    public function hapus($id_user) {
        if ($id_user == $this->session->userdata('id_user')) {
            $this->session->set_flashdata('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            redirect('users');
        }

        $this->User_model->delete($id_user);
        $this->session->set_flashdata('success', 'Pengguna berhasil dihapus.');
        redirect('users');
    }
}
