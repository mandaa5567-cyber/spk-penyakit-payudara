/**
 * CDSS Kanker Payudara - Clinical Decision Support System
 * Real-Time Rule-Based Reasoning Engine & UI Interactions
 */

document.addEventListener('DOMContentLoaded', function () {
  // Mobile sidebar toggle
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('show');
    });
  }

  // Patient selector in screening form
  const pasienSelect = document.getElementById('id_pasien_select');
  const patientDobInput = document.getElementById('pasien_dob_hidden');
  const usiaInput = document.getElementById('usia_input');

  if (pasienSelect) {
    pasienSelect.addEventListener('change', function () {
      const selected = this.options[this.selectedIndex];
      const dob = selected.getAttribute('data-dob');
      if (dob) {
        const birthDate = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
          age--;
        }
        if (usiaInput) {
          usiaInput.value = age;
          usiaInput.dispatchEvent(new Event('input'));
        }
      }
    });
  }

  // Live screening form calculation
  initLiveScreeningEngine();
});

/**
 * Real-time Inference Engine (Rule-Based Reasoning with Forward Chaining)
 */
function initLiveScreeningEngine() {
  const screeningForm = document.getElementById('screeningForm');
  if (!screeningForm) return;

  const bbInput = document.getElementById('berat_badan');
  const tbInput = document.getElementById('tinggi_badan');
  const imtDisplay = document.getElementById('imt_display');
  const imtCategoryBadge = document.getElementById('imt_category_badge');
  const statusMenopause = document.getElementById('status_menopause');
  const menopauseAgeWrapper = document.getElementById('menopause_age_wrapper');
  const usiaMenopause = document.getElementById('usia_menopause');

  const liveScoreEl = document.getElementById('liveScore');
  const liveCategoryEl = document.getElementById('liveCategory');
  const liveRecommendationEl = document.getElementById('liveRecommendation');
  const liveOverrideBanner = document.getElementById('liveOverrideBanner');
  const liveTriggeredRulesList = document.getElementById('liveTriggeredRules');

  // Menopause field toggle
  if (statusMenopause && menopauseAgeWrapper) {
    statusMenopause.addEventListener('change', function () {
      if (this.value === 'Sudah menopause') {
        menopauseAgeWrapper.style.display = 'block';
      } else {
        menopauseAgeWrapper.style.display = 'none';
        if (usiaMenopause) usiaMenopause.value = '';
      }
      evaluateLiveRules();
    });
  }

  // Form input listeners
  screeningForm.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('input', evaluateLiveRules);
    el.addEventListener('change', evaluateLiveRules);
  });

  function calculateIMT() {
    const bb = parseFloat(bbInput ? bbInput.value : 0) || 0;
    const tb = parseFloat(tbInput ? tbInput.value : 0) || 0;
    if (tb > 0 && bb > 0) {
      const tbM = tb / 100;
      const imt = (bb / (tbM * tbM)).toFixed(1);
      if (imtDisplay) imtDisplay.innerText = imt;

      if (imtCategoryBadge) {
        if (imt < 18.5) {
          imtCategoryBadge.innerText = 'Kurus (< 18.5)';
          imtCategoryBadge.className = 'badge badge-navy';
        } else if (imt < 25) {
          imtCategoryBadge.innerText = 'Normal (18.5 - 24.9)';
          imtCategoryBadge.className = 'badge badge-low';
        } else if (imt < 30) {
          imtCategoryBadge.innerText = 'Kelebihan BB (25.0 - 29.9)';
          imtCategoryBadge.className = 'badge badge-med';
        } else {
          imtCategoryBadge.innerText = 'Obesitas (≥ 30.0)';
          imtCategoryBadge.className = 'badge badge-high';
        }
      }
      return parseFloat(imt);
    } else {
      if (imtDisplay) imtDisplay.innerText = '-';
      if (imtCategoryBadge) {
        imtCategoryBadge.innerText = 'Belum lengkap';
        imtCategoryBadge.className = 'badge badge-navy';
      }
      return 0;
    }
  }

  function evaluateLiveRules() {
    const imt = calculateIMT();
    let totalScore = 0;
    let overrideActive = false;
    let activeRules = [];

    // F01: Usia >= 50 (Bobot 2)
    const usiaVal = parseFloat(document.getElementById('usia_input') ? document.getElementById('usia_input').value : 0) || 0;
    const rowF01 = document.getElementById('row_F01');
    if (usiaVal >= 50) {
      totalScore += 2;
      activeRules.push({ id: 'R01', factor: 'F01 (Usia ≥ 50 th)', score: 2 });
      if (rowF01) rowF01.classList.add('active-risk');
    } else {
      if (rowF01) rowF01.classList.remove('active-risk');
    }

    // F02: Riwayat keluarga derajat pertama (Bobot 3)
    const f02Select = document.getElementById('f_F02');
    const f02Val = f02Select ? f02Select.value : '';
    const rowF02 = document.getElementById('row_F02');
    if (['Ibu', 'Saudari kandung', 'Anak'].includes(f02Val)) {
      totalScore += 3;
      activeRules.push({ id: 'R02', factor: 'F02 (Keluarga: ' + f02Val + ')', score: 3 });
      if (rowF02) rowF02.classList.add('active-risk');
    } else {
      if (rowF02) rowF02.classList.remove('active-risk');
    }

    // F03: Riwayat Ca payudara pribadi (Bobot 4, OVERRIDE)
    const f03Check = document.getElementById('f_F03');
    const rowF03 = document.getElementById('row_F03');
    if (f03Check && f03Check.checked) {
      totalScore += 4;
      overrideActive = true;
      activeRules.push({ id: 'R03/R16', factor: 'F03 (Riwayat Ca Pribadi - OVERRIDE)', score: 4 });
      if (rowF03) rowF03.classList.add('active-override');
    } else {
      if (rowF03) rowF03.classList.remove('active-override');
    }

    // F04: Mutasi BRCA1/BRCA2 (Bobot 5, OVERRIDE)
    const f04Check = document.getElementById('f_F04');
    const rowF04 = document.getElementById('row_F04');
    if (f04Check && f04Check.checked) {
      totalScore += 5;
      overrideActive = true;
      activeRules.push({ id: 'R04/R16', factor: 'F04 (Mutasi BRCA1/2 - OVERRIDE)', score: 5 });
      if (rowF04) rowF04.classList.add('active-override');
    } else {
      if (rowF04) rowF04.classList.remove('active-override');
    }

    // F05: Menarche dini < 12 th (Bobot 1)
    const f05Input = document.getElementById('f_F05');
    const f05Val = parseFloat(f05Input ? f05Input.value : 0) || 0;
    const rowF05 = document.getElementById('row_F05');
    if (f05Val > 0 && f05Val < 12) {
      totalScore += 1;
      activeRules.push({ id: 'R05', factor: 'F05 (Menarche < 12 th)', score: 1 });
      if (rowF05) rowF05.classList.add('active-risk');
    } else {
      if (rowF05) rowF05.classList.remove('active-risk');
    }

    // F06: Menopause terlambat > 55 th (Bobot 1)
    const isMenopause = statusMenopause && statusMenopause.value === 'Sudah menopause';
    const f06Val = parseFloat(usiaMenopause ? usiaMenopause.value : 0) || 0;
    const rowF06 = document.getElementById('row_F06');
    if (isMenopause && f06Val > 55) {
      totalScore += 1;
      activeRules.push({ id: 'R06', factor: 'F06 (Menopause > 55 th)', score: 1 });
      if (rowF06) rowF06.classList.add('active-risk');
    } else {
      if (rowF06) rowF06.classList.remove('active-risk');
    }

    // F07: Nulipara / anak pertama > 30 th (Bobot 1)
    const f07Check = document.getElementById('f_F07');
    const rowF07 = document.getElementById('row_F07');
    if (f07Check && f07Check.checked) {
      totalScore += 1;
      activeRules.push({ id: 'R07', factor: 'F07 (Nulipara / Anak 1 > 30 th)', score: 1 });
      if (rowF07) rowF07.classList.add('active-risk');
    } else {
      if (rowF07) rowF07.classList.remove('active-risk');
    }

    // F08: Tidak pernah menyusui (Bobot 1)
    const f08Check = document.getElementById('f_F08');
    const rowF08 = document.getElementById('row_F08');
    if (f08Check && f08Check.checked) {
      totalScore += 1;
      activeRules.push({ id: 'R08', factor: 'F08 (Tidak Pernah Menyusui)', score: 1 });
      if (rowF08) rowF08.classList.add('active-risk');
    } else {
      if (rowF08) rowF08.classList.remove('active-risk');
    }

    // F09: Kontrasepsi hormonal / TSH jangka panjang (Bobot 1)
    const f09Check = document.getElementById('f_F09');
    const rowF09 = document.getElementById('row_F09');
    if (f09Check && f09Check.checked) {
      totalScore += 1;
      activeRules.push({ id: 'R09', factor: 'F09 (Kontrasepsi/TSH Jangka Panjang)', score: 1 });
      if (rowF09) rowF09.classList.add('active-risk');
    } else {
      if (rowF09) rowF09.classList.remove('active-risk');
    }

    // F10: Obesitas IMT >= 30 (Bobot 1)
    const rowF10 = document.getElementById('row_F10');
    if (imt >= 30) {
      totalScore += 1;
      activeRules.push({ id: 'R10', factor: 'F10 (Obesitas IMT ≥ 30)', score: 1 });
      if (rowF10) rowF10.classList.add('active-risk');
    } else {
      if (rowF10) rowF10.classList.remove('active-risk');
    }

    // F11: Konsumsi alkohol = Rutin (Bobot 1)
    const f11Select = document.getElementById('f_F11');
    const f11Val = f11Select ? f11Select.value : '';
    const rowF11 = document.getElementById('row_F11');
    if (f11Val === 'Rutin') {
      totalScore += 1;
      activeRules.push({ id: 'R11', factor: 'F11 (Konsumsi Alkohol Rutin)', score: 1 });
      if (rowF11) rowF11.classList.add('active-risk');
    } else {
      if (rowF11) rowF11.classList.remove('active-risk');
    }

    // F12: Aktivitas fisik = Kurang (Bobot 1)
    const f12Select = document.getElementById('f_F12');
    const f12Val = f12Select ? f12Select.value : '';
    const rowF12 = document.getElementById('row_F12');
    if (f12Val === 'Kurang') {
      totalScore += 1;
      activeRules.push({ id: 'R12', factor: 'F12 (Aktivitas Fisik Kurang)', score: 1 });
      if (rowF12) rowF12.classList.add('active-risk');
    } else {
      if (rowF12) rowF12.classList.remove('active-risk');
    }

    // F13: Biopsi hiperplasia atipikal (Bobot 3)
    const f13Check = document.getElementById('f_F13');
    const rowF13 = document.getElementById('row_F13');
    if (f13Check && f13Check.checked) {
      totalScore += 3;
      activeRules.push({ id: 'R13', factor: 'F13 (Hiperplasia Atipikal)', score: 3 });
      if (rowF13) rowF13.classList.add('active-risk');
    } else {
      if (rowF13) rowF13.classList.remove('active-risk');
    }

    // F14: Radioterapi area dada (Bobot 3, OVERRIDE)
    const f14Check = document.getElementById('f_F14');
    const rowF14 = document.getElementById('row_F14');
    if (f14Check && f14Check.checked) {
      totalScore += 3;
      overrideActive = true;
      activeRules.push({ id: 'R14/R16', factor: 'F14 (Radioterapi Dada - OVERRIDE)', score: 3 });
      if (rowF14) rowF14.classList.add('active-override');
    } else {
      if (rowF14) rowF14.classList.remove('active-override');
    }

    // F15: Densitas payudara BI-RADS C atau D (Bobot 2)
    const f15Select = document.getElementById('f_F15');
    const f15Val = f15Select ? f15Select.value : '';
    const rowF15 = document.getElementById('row_F15');
    if (['C', 'D'].includes(f15Val)) {
      totalScore += 2;
      activeRules.push({ id: 'R15', factor: 'F15 (Densitas BI-RADS ' + f15Val + ')', score: 2 });
      if (rowF15) rowF15.classList.add('active-risk');
    } else {
      if (rowF15) rowF15.classList.remove('active-risk');
    }

    // Update Live Score display
    if (liveScoreEl) liveScoreEl.innerText = totalScore;

    // Determine category
    let category = 'Rendah';
    let catClass = 'badge-low';
    let recommendation = 'Edukasi dan SADARI rutin setiap bulan; pemeriksaan klinis payudara (SADANIS) berkala; ulangi skrining sesuai jadwal.';

    if (overrideActive || totalScore >= 8) {
      category = 'Tinggi';
      catClass = 'badge-high';
      recommendation = 'Rujuk ke dokter spesialis untuk evaluasi lanjut (mis. mamografi/USG, konseling genetik bila diperlukan).';
    } else if (totalScore >= 4) {
      category = 'Sedang';
      catClass = 'badge-med';
      recommendation = 'SADARI + SADANIS oleh tenaga kesehatan secara berkala; pertimbangkan USG/mamografi sesuai usia dan saran dokter.';
    }

    // Update Category badge
    if (liveCategoryEl) {
      liveCategoryEl.innerText = 'RISIKO ' + category.toUpperCase();
      liveCategoryEl.className = 'live-risk-badge ' + catClass;
    }

    // Update Recommendation text
    if (liveRecommendationEl) {
      liveRecommendationEl.innerText = recommendation;
    }

    // Override banner toggle
    if (liveOverrideBanner) {
      liveOverrideBanner.style.display = overrideActive ? 'block' : 'none';
    }

    // Update active rules list
    if (liveTriggeredRulesList) {
      if (activeRules.length === 0) {
        liveTriggeredRulesList.innerHTML = '<div style="font-size:12px; color:#94A3B8; text-align:center; padding:10px;">Belum ada aturan yang terpenuhi</div>';
      } else {
        liveTriggeredRulesList.innerHTML = activeRules.map(r => `
          <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; padding:6px 0; border-bottom:1px dashed #E2E8F0;">
            <span style="font-weight:600; color:#0F172A;">
              <span class="badge badge-navy" style="font-size:10px; padding:2px 5px;">${r.id}</span> ${r.factor}
            </span>
            <span style="font-weight:700; color:#2563EB;">+${r.score}</span>
          </div>
        `).join('');
      }
    }
  }

  // Initial calculation trigger
  evaluateLiveRules();
}

/**
 * Modal functions
 */
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('show');
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('show');
  }
}

// Close modal if clicked on backdrop
window.addEventListener('click', function (e) {
  if (e.target.classList.contains('modal-backdrop')) {
    e.target.classList.remove('show');
  }
});
