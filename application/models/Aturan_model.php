<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aturan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_faktor() {
        return $this->db->order_by('id_faktor', 'ASC')->get('faktor_risiko')->result_array();
    }

    public function get_faktor_by_id($id_faktor) {
        return $this->db->get_where('faktor_risiko', ['id_faktor' => $id_faktor])->row_array();
    }

    public function update_faktor($id_faktor, $data) {
        return $this->db->where('id_faktor', $id_faktor)->update('faktor_risiko', $data);
    }

    public function get_all_kategori() {
        return $this->db->order_by('skor_min', 'ASC')->get('kategori_risiko')->result_array();
    }

    public function get_kategori_by_id($id_kategori) {
        return $this->db->get_where('kategori_risiko', ['id_kategori' => $id_kategori])->row_array();
    }

    public function update_kategori($id_kategori, $data) {
        return $this->db->where('id_kategori', $id_kategori)->update('kategori_risiko', $data);
    }

    public function get_rekap_kategori() {
        $result = $this->db->get('v_rekap_kategori')->result_array();
        if (empty($result)) {
            // fallback in case view is not populated
            $query = "SELECT k.nama_kategori, COUNT(s.id_skrining) AS jumlah 
                      FROM kategori_risiko k 
                      LEFT JOIN skrining s ON s.id_kategori = k.id_kategori 
                      GROUP BY k.id_kategori, k.nama_kategori, k.skor_min 
                      ORDER BY k.skor_min ASC";
            return $this->db->query($query)->result_array();
        }
        return $result;
    }

    public function get_frekuensi_faktor() {
        $result = $this->db->get('v_frekuensi_faktor')->result_array();
        if (empty($result)) {
            $total_skrining = (int)$this->db->count_all('skrining');
            $query = "SELECT f.id_faktor, f.nama_faktor, f.bobot, f.override_tinggi, 
                             COALESCE(SUM(d.terpenuhi), 0) AS jumlah_pasien,
                             ROUND(COALESCE(SUM(d.terpenuhi), 0) * 100.0 / NULLIF($total_skrining, 0), 1) AS persentase
                      FROM faktor_risiko f 
                      LEFT JOIN detail_skrining d ON d.id_faktor = f.id_faktor 
                      GROUP BY f.id_faktor, f.nama_faktor, f.bobot, f.override_tinggi 
                      ORDER BY f.id_faktor ASC";
            return $this->db->query($query)->result_array();
        }
        return $result;
    }

    public function get_data_pasien_lengkap() {
        return $this->db->get('v_data_pasien_lengkap')->result_array();
    }

    public function get_all_rules() {
        return [
            ['id' => 'R01', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Usia pasien ≥ 50 tahun', 'kesimpulan' => 'Faktor F01 terpenuhi, tambah skor +2', 'bobot' => 2, 'override' => 'Tidak'],
            ['id' => 'R02', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Hubungan kerabat penderita = Ibu, Saudari kandung, atau Anak', 'kesimpulan' => 'Faktor F02 terpenuhi, tambah skor +3', 'bobot' => 3, 'override' => 'Tidak'],
            ['id' => 'R03', 'tahap' => 'Penilaian faktor & Override', 'kondisi' => 'Riwayat kanker payudara pribadi sebelumnya = Ya', 'kesimpulan' => 'Faktor F03 terpenuhi, tambah skor +4; Picu Override Tinggi', 'bobot' => 4, 'override' => 'Ya'],
            ['id' => 'R04', 'tahap' => 'Penilaian faktor & Override', 'kondisi' => 'Mutasi gen BRCA1/BRCA2 diketahui positif = Ya', 'kesimpulan' => 'Faktor F04 terpenuhi, tambah skor +5; Picu Override Tinggi', 'bobot' => 5, 'override' => 'Ya'],
            ['id' => 'R05', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Usia menarche < 12 tahun', 'kesimpulan' => 'Faktor F05 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R06', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Status = Sudah menopause DAN usia menopause > 55 tahun', 'kesimpulan' => 'Faktor F06 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R07', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Nulipara / melahirkan anak pertama usia > 30 tahun = Ya', 'kesimpulan' => 'Faktor F07 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R08', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Tidak pernah menyusui ASI = Ya', 'kesimpulan' => 'Faktor F08 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R09', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Kontrasepsi hormonal / terapi sulih hormon jangka panjang = Ya', 'kesimpulan' => 'Faktor F09 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R10', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Indeks Massa Tubuh (IMT) ≥ 30.0 kg/m²', 'kesimpulan' => 'Faktor F10 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R11', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Konsumsi minuman beralkohol = Rutin', 'kesimpulan' => 'Faktor F11 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R12', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Aktivitas fisik / olahraga = Kurang', 'kesimpulan' => 'Faktor F12 terpenuhi, tambah skor +1', 'bobot' => 1, 'override' => 'Tidak'],
            ['id' => 'R13', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Riwayat biopsi payudara hiperplasia atipikal = Ya', 'kesimpulan' => 'Faktor F13 terpenuhi, tambah skor +3', 'bobot' => 3, 'override' => 'Tidak'],
            ['id' => 'R14', 'tahap' => 'Penilaian faktor & Override', 'kondisi' => 'Riwayat radioterapi di area dada = Ya', 'kesimpulan' => 'Faktor F14 terpenuhi, tambah skor +3; Picu Override Tinggi', 'bobot' => 3, 'override' => 'Ya'],
            ['id' => 'R15', 'tahap' => 'Penilaian faktor', 'kondisi' => 'Densitas jaringan payudara (BI-RADS) = C atau D', 'kesimpulan' => 'Faktor F15 terpenuhi, tambah skor +2', 'bobot' => 2, 'override' => 'Tidak'],
            ['id' => 'R16', 'tahap' => 'Override', 'kondisi' => 'Faktor F03, F04, atau F14 terpenuhi (kolom Override = Ya)', 'kesimpulan' => 'Kategori = Tinggi, tanpa memandang total skor yang terkumpul', 'bobot' => '-', 'override' => 'Ya'],
            ['id' => 'R17', 'tahap' => 'Agregasi', 'kondisi' => 'Semua 15 faktor risiko telah selesai dievaluasi', 'kesimpulan' => 'Total skor = ∑(faktor terpenuhi × bobot faktor)', 'bobot' => '-', 'override' => 'Tidak'],
            ['id' => 'R18', 'tahap' => 'Klasifikasi', 'kondisi' => 'Total skor antara 0 dan 3 (tanpa aturan override aktif)', 'kesimpulan' => 'Kategori = Rendah; Berikan rekomendasi kategori Rendah', 'bobot' => '-', 'override' => 'Tidak'],
            ['id' => 'R19', 'tahap' => 'Klasifikasi', 'kondisi' => 'Total skor antara 4 dan 7 (tanpa aturan override aktif)', 'kesimpulan' => 'Kategori = Sedang; Berikan rekomendasi kategori Sedang', 'bobot' => '-', 'override' => 'Tidak'],
            ['id' => 'R20', 'tahap' => 'Klasifikasi', 'kondisi' => 'Total skor ≥ 8 ATAU aturan override aktif', 'kesimpulan' => 'Kategori = Tinggi; Berikan rekomendasi kategori Tinggi', 'bobot' => '-', 'override' => 'Ya']
        ];
    }

    public function get_kebutuhan_sistem() {
        return [
            ['id' => 'FR-01', 'jenis' => 'Fungsional', 'kebutuhan' => 'Autentikasi pengguna', 'deskripsi' => 'Login/logout dengan peran Admin dan Tenaga Kesehatan', 'aktor' => 'Admin, Nakes', 'prioritas' => 'Tinggi'],
            ['id' => 'FR-02', 'jenis' => 'Fungsional', 'kebutuhan' => 'Manajemen data pasien', 'deskripsi' => 'Tambah, ubah, lihat, dan cari data pasien', 'aktor' => 'Nakes, Admin', 'prioritas' => 'Tinggi'],
            ['id' => 'FR-03', 'jenis' => 'Fungsional', 'kebutuhan' => 'Input faktor risiko', 'deskripsi' => 'Formulir pengisian 15 faktor risiko dengan validasi input', 'aktor' => 'Nakes', 'prioritas' => 'Tinggi'],
            ['id' => 'FR-04', 'jenis' => 'Fungsional', 'kebutuhan' => 'Inferensi rule-based', 'deskripsi' => 'Mesin inferensi forward chaining menghitung skor dan kategori risiko', 'aktor' => 'Sistem', 'prioritas' => 'Tinggi'],
            ['id' => 'FR-05', 'jenis' => 'Fungsional', 'kebutuhan' => 'Rekomendasi tindak lanjut', 'deskripsi' => 'Menampilkan rekomendasi klinis sesuai kategori risiko', 'aktor' => 'Sistem', 'prioritas' => 'Tinggi'],
            ['id' => 'FR-06', 'jenis' => 'Fungsional', 'kebutuhan' => 'Manajemen basis aturan', 'deskripsi' => 'Admin mengubah bobot, ambang, dan aturan tanpa mengubah kode program', 'aktor' => 'Admin', 'prioritas' => 'Sedang'],
            ['id' => 'FR-07', 'jenis' => 'Fungsional', 'kebutuhan' => 'Riwayat skrining', 'deskripsi' => 'Menyimpan dan menampilkan riwayat hasil skrining per pasien', 'aktor' => 'Nakes, Admin', 'prioritas' => 'Sedang'],
            ['id' => 'FR-08', 'jenis' => 'Fungsional', 'kebutuhan' => 'Cetak/ekspor hasil', 'deskripsi' => 'Ekspor hasil skrining ke PDF / lembar cetak standar medis & CSV', 'aktor' => 'Nakes, Admin', 'prioritas' => 'Sedang'],
            ['id' => 'FR-09', 'jenis' => 'Fungsional', 'kebutuhan' => 'Dashboard ringkasan', 'deskripsi' => 'Grafik jumlah skrining per kategori risiko & frekuensi faktor', 'aktor' => 'Admin, Nakes', 'prioritas' => 'Rendah'],
            ['id' => 'NFR-01', 'jenis' => 'Non-fungsional', 'kebutuhan' => 'Keamanan data', 'deskripsi' => 'Kata sandi ter-hash (Bcrypt), kontrol akses berbasis peran (RBAC)', 'aktor' => 'Sistem', 'prioritas' => 'Tinggi'],
            ['id' => 'NFR-02', 'jenis' => 'Non-fungsional', 'kebutuhan' => 'Usability', 'deskripsi' => 'Antarmuka responsif dan mudah dipakai tenaga kesehatan (standar SUS)', 'aktor' => 'Sistem', 'prioritas' => 'Sedang'],
            ['id' => 'NFR-03', 'jenis' => 'Non-fungsional', 'kebutuhan' => 'Kinerja', 'deskripsi' => 'Hasil inferensi live preview realtime tampil ≤ 3 detik', 'aktor' => 'Sistem', 'prioritas' => 'Sedang'],
            ['id' => 'NFR-04', 'jenis' => 'Non-fungsional', 'kebutuhan' => 'Kompatibilitas', 'deskripsi' => 'Dapat diakses melalui peramban modern di desktop dan perangkat seluler', 'aktor' => 'Sistem', 'prioritas' => 'Sedang'],
            ['id' => 'NFR-05', 'jenis' => 'Non-fungsional', 'kebutuhan' => 'Akurasi aturan', 'deskripsi' => 'Hasil sistem divalidasi terhadap pakar dokter onkologi / bedah', 'aktor' => 'Pakar Medis', 'prioritas' => 'Tinggi']
        ];
    }
}
