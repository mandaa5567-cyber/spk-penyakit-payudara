<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Login Petugas - CDSS Kanker Payudara'); ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
  <style>
    body {
      background: radial-gradient(circle at 10% 20%, #0F223D 0%, #0B192C 90%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .login-container {
      width: 100%;
      max-width: 960px;
      background: #FFFFFF;
      border-radius: var(--radius-xl);
      box-shadow: 0 25px 50px -12px rgba(6, 14, 26, 0.45);
      display: grid;
      grid-template-columns: 1fr 1fr;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .login-sidebar {
      background: linear-gradient(145deg, #0B192C 0%, #1E3E62 100%);
      color: #FFFFFF;
      padding: 48px 40px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }
    .login-sidebar::after {
      content: '';
      position: absolute;
      bottom: -60px;
      right: -60px;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, transparent 70%);
      border-radius: 50%;
    }
    .login-form-wrapper {
      padding: 48px 44px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #FFFFFF;
    }
    .quick-account-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      background: var(--navy-50);
      border: 1.5px solid var(--border-light);
      border-radius: var(--radius-md);
      cursor: pointer;
      transition: var(--transition);
      text-align: left;
      margin-bottom: 10px;
      width: 100%;
    }
    .quick-account-btn:hover {
      background: #FFFFFF;
      border-color: var(--blue-accent);
      transform: translateY(-2px);
      box-shadow: var(--shadow-sm);
    }
    @media (max-width: 768px) {
      .login-container {
        grid-template-columns: 1fr;
      }
      .login-sidebar {
        display: none;
      }
      .login-form-wrapper {
        padding: 32px 24px;
      }
    }
  </style>
</head>
<body>

<div class="login-container">
  <!-- Left Side: Branding & Info -->
  <div class="login-sidebar">
    <div>
      <div style="display:flex; align-items:center; gap:14px; margin-bottom:28px;">
        <div class="brand-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            <path d="M12 7v6m-3-3h6" stroke="#FFFFFF" stroke-width="2.5"/>
          </svg>
        </div>
        <div>
          <h2 style="font-size:18px; font-weight:700; color:#FFFFFF;">CDSS Skrining</h2>
          <p style="font-size:12px; color:#93B4DC; text-transform:uppercase; letter-spacing:0.05em;">Kanker Payudara</p>
        </div>
      </div>

      <h3 style="font-size:24px; font-weight:800; line-height:1.25; margin-bottom:14px; letter-spacing:-0.02em;">
        Sistem Pendukung Keputusan Klinis
      </h3>
      <p style="font-size:14px; color:#C6D9F0; line-height:1.6; margin-bottom:24px;">
        Membantu tenaga kesehatan dan dokter spesialis menilai risiko kanker payudara (Rendah, Sedang, Tinggi) secara akurat menggunakan mesin inferensi <strong>Rule-Based Reasoning</strong> dan aturan klinis pakar.
      </p>

      <div style="background: rgba(255,255,255,0.08); padding:16px; border-radius:var(--radius-md); border:1px solid rgba(255,255,255,0.12);">
        <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:#38BDF8; margin-bottom:6px; letter-spacing:0.05em;">
          Fitur Utama Sistem
        </div>
        <ul style="font-size:13px; color:#E8F0F9; padding-left:18px; line-height:1.6;">
          <li>15 Parameter Faktor Risiko Terstandar</li>
          <li>Inferensi Real-Time Forward Chaining</li>
          <li>Aturan Klinis Override Otomatis (BRCA/Radioterapi)</li>
          <li>Cetak Lembar Rekam Medis & Rekomendasi</li>
        </ul>
      </div>
    </div>

    <div style="font-size:12px; color:#93B4DC; margin-top:30px;">
      &copy; <?= date('Y'); ?> Clinical Decision Support System. Seluruh Hak Dilindungi.
    </div>
  </div>

  <!-- Right Side: Login Form -->
  <div class="login-form-wrapper">
    <div style="margin-bottom: 24px;">
      <h2 style="font-size: 22px; font-weight: 800; color: var(--navy-900); letter-spacing:-0.02em;">Masuk ke Portal Klinis</h2>
      <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">
        Gunakan akun petugas atau pilih akun demo di bawah ini.
      </p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error" style="margin-bottom:18px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div><?= htmlspecialchars($error); ?></div>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/login'); ?>" method="POST">
      <div class="form-group">
        <label class="form-label" for="username">Username Petugas</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required autofocus value="admin">
      </div>

      <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label" for="password">Kata Sandi</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required value="admin123">
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-size:15px; margin-bottom:20px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        <span>Masuk ke Sistem</span>
      </button>
    </form>

    <!-- Quick 1-Click Login Helper for Demo -->
    <div style="border-top:1px dashed var(--border-light); padding-top:20px;">
      <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
        Akses Cepat Mode Uji Coba:
      </div>
      
      <a href="<?= base_url('quick-login/admin'); ?>" class="quick-account-btn">
        <div style="width:34px; height:34px; border-radius:50%; background:#0EA5E9; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">
          Dr
        </div>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:var(--navy-900);">dr. Hendra / dr. Rian (Admin Onkologi)</div>
          <div style="font-size:11.5px; color:var(--text-muted);">Akses penuh (Skrining, Basis Aturan & Bobot)</div>
        </div>
        <span class="badge badge-navy" style="font-size:11px;">Admin &rarr;</span>
      </a>

      <a href="<?= base_url('quick-login/nakes'); ?>" class="quick-account-btn">
        <div style="width:34px; height:34px; border-radius:50%; background:#10B981; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">
          Ns
        </div>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:var(--navy-900);">Ns. Maya Indah (Tenaga Kesehatan)</div>
          <div style="font-size:11.5px; color:var(--text-muted);">Akses klinis (Skrining, Pasien, Riwayat & Cetak)</div>
        </div>
        <span class="badge badge-low" style="font-size:11px;">Nakes &rarr;</span>
      </a>
    </div>
  </div>
</div>

</body>
</html>
