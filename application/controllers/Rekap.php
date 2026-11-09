<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap extends CI_Controller {

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
        $data['title'] = 'Rekapitulasi & Statistik Hasil Skrining (CDSS)';
        $data['active_menu'] = 'rekap';
        $data['user'] = [
            'nama' => $this->session->userdata('nama'),
            'role' => $this->session->userdata('role')
        ];

        $data['stats'] = $this->Skrining_model->get_stats();
        $data['rekap_kategori'] = $this->Aturan_model->get_rekap_kategori();
        $data['frekuensi_faktor'] = $this->Aturan_model->get_frekuensi_faktor();
        $data['matrix_data'] = $this->Aturan_model->get_data_pasien_lengkap();

        $this->load->view('layout/header', $data);
        $this->load->view('layout/sidebar', $data);
        $this->load->view('layout/topbar', $data);
        $this->load->view('rekap/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function export_rekap_csv() {
        $stats = $this->Skrining_model->get_stats();
        $rekap_kategori = $this->Aturan_model->get_rekap_kategori();
        $frekuensi_faktor = $this->Aturan_model->get_frekuensi_faktor();

        $filename = 'Rekap_Hasil_Skrining_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $out = fopen('php://output', 'w');
        // BOM for Excel utf-8
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, ['REKAP HASIL SKRINING RISIKO KANKER PAYUDARA (CDSS)']);
        fputcsv($out, ['Tanggal Unduh', date('d/m/Y H:i:s')]);
        fputcsv($out, []);

        fputcsv($out, ['Jumlah Pasien Terskrining', $stats['total_skrining']]);
        fputcsv($out, ['Rata-rata Total Skor', $stats['avg_skor']]);
        fputcsv($out, []);

        fputcsv($out, ['--- REKAP KATEGORI RISIKO ---']);
        fputcsv($out, ['Kategori Risiko', 'Skor Minimum', 'Jumlah Pasien', 'Persentase (%)', 'Rekomendasi']);
        foreach ($rekap_kategori as $k) {
            fputcsv($out, [
                $k['nama_kategori'],
                $k['skor_min'],
                $k['jumlah'],
                $k['persentase'] . '%',
                $k['rekomendasi']
            ]);
        }
        fputcsv($out, ['Total', '', $stats['total_skrining'], '100%']);
        fputcsv($out, []);

        fputcsv($out, ['--- FREKUENSI FAKTOR RISIKO ---']);
        fputcsv($out, ['ID Faktor', 'Nama Faktor Risiko', 'Bobot', 'Override Tinggi', 'Jumlah Pasien Terpenuhi', 'Persentase (%)']);
        foreach ($frekuensi_faktor as $f) {
            fputcsv($out, [
                $f['id_faktor'],
                $f['nama_faktor'],
                $f['bobot'],
                $f['override_tinggi'] ? 'Ya' : 'Tidak',
                $f['jumlah_pasien'],
                $f['persentase'] . '%'
            ]);
        }

        fclose($out);
        exit;
    }

    public function export_matrix_csv() {
        $matrix = $this->Aturan_model->get_data_pasien_lengkap();
        $filename = 'Register_Data_Pasien_Lengkap_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");

        // Header sesuai Sheet Data_Pasien Excel
        $headers = [
            'No', 'Tanggal Skrining', 'No. RM', 'Nama Pasien', 'Tanggal Lahir', 'Usia Saat Skrining',
            'Status Menopause', 'BB (kg)', 'TB (cm)', 'IMT', 'Total Skor', 'Override Aktif', 'Kategori Risiko', 'Petugas',
            'F01 (Usia)', 'F01 Terpenuhi',
            'F02 (Kerabat)', 'F02 Terpenuhi',
            'F03 (Ca Pribadi)', 'F03 Terpenuhi',
            'F04 (BRCA)', 'F04 Terpenuhi',
            'F05 (Menarche)', 'F05 Terpenuhi',
            'F06 (Menopause)', 'F06 Terpenuhi',
            'F07 (Nulipara)', 'F07 Terpenuhi',
            'F08 (Menyusui)', 'F08 Terpenuhi',
            'F09 (KB/TSH)', 'F09 Terpenuhi',
            'F10 (IMT/Obesitas)', 'F10 Terpenuhi',
            'F11 (Alkohol)', 'F11 Terpenuhi',
            'F12 (Aktivitas)', 'F12 Terpenuhi',
            'F13 (Hiperplasia)', 'F13 Terpenuhi',
            'F14 (Radioterapi)', 'F14 Terpenuhi',
            'F15 (BI-RADS)', 'F15 Terpenuhi',
            'Rekomendasi Tindak Lanjut'
        ];
        fputcsv($out, $headers);

        $no = 1;
        foreach ($matrix as $row) {
            fputcsv($out, [
                $no++,
                date('d/m/Y H:i', strtotime($row['tanggal_skrining'])),
                $row['no_rm'],
                $row['nama_pasien'],
                $row['tanggal_lahir'] ? date('d/m/Y', strtotime($row['tanggal_lahir'])) : '-',
                $row['usia_saat_skrining'],
                $row['status_menopause'],
                $row['berat_badan'],
                $row['tinggi_badan'],
                $row['imt'],
                $row['total_skor'],
                $row['override_aktif'],
                $row['kategori_risiko'],
                $row['petugas'],
                $row['f01_input'], $row['f01_terpenuhi'],
                $row['f02_input'], $row['f02_terpenuhi'],
                $row['f03_input'], $row['f03_terpenuhi'],
                $row['f04_input'], $row['f04_terpenuhi'],
                $row['f05_input'], $row['f05_terpenuhi'],
                $row['f06_input'], $row['f06_terpenuhi'],
                $row['f07_input'], $row['f07_terpenuhi'],
                $row['f08_input'], $row['f08_terpenuhi'],
                $row['f09_input'], $row['f09_terpenuhi'],
                $row['f10_input'], $row['f10_terpenuhi'],
                $row['f11_input'], $row['f11_terpenuhi'],
                $row['f12_input'], $row['f12_terpenuhi'],
                $row['f13_input'], $row['f13_terpenuhi'],
                $row['f14_input'], $row['f14_terpenuhi'],
                $row['f15_input'], $row['f15_terpenuhi'],
                $row['rekomendasi']
            ]);
        }

        fclose($out);
        exit;
    }
}
