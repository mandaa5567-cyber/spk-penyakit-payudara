<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {
        return $this->db->order_by('nama', 'ASC')->get('users')->result_array();
    }

    public function get_by_id($id_user) {
        return $this->db->get_where('users', ['id_user' => $id_user])->row_array();
    }

    public function get_by_username($username) {
        return $this->db->get_where('users', ['username' => $username])->row_array();
    }

    public function verify_login($username, $password) {
        $user = $this->get_by_username($username);
        if (!$user) {
            return false;
        }

        // Support standard bcrypt hash and plain text fallback for initial seeding
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            return $user;
        }
        return false;
    }

    public function insert($data) {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function update($id_user, $data) {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        return $this->db->where('id_user', $id_user)->update('users', $data);
    }

    public function delete($id_user) {
        return $this->db->where('id_user', $id_user)->delete('users');
    }
}
