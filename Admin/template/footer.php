<!--begin::Footer-->
<footer class="app-footer">
  <div class="float-end d-none d-sm-inline">
    <strong style="font-family:'Outfit',sans-serif;">XTM Ganakat</strong>
  </div>
  <strong style="font-family:'Outfit',sans-serif;">
    Copyright &copy; 2025&nbsp;
    <a href="hal_admin.php" class="text-decoration-none">XTM Ganakat</a>.
  </strong>
  All rights reserved.
</footer>
<!--end::Footer-->
</div>
<!--end::App Wrapper-->

<!-- ── Scripts ───────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="./assets/dist/js/adminlte.js"></script>

<!-- OverlayScrollbars config -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars) {
      OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
        scrollbars: { theme: 'os-theme-light', autoHide: 'leave', clickScroll: true },
      });
    }
  });
</script>

<!-- ApexCharts (untuk dashboard) -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js" crossorigin="anonymous"></script>

<!-- ════════════════════════════════════════
     SEARCH OVERLAY LOGIC
════════════════════════════════════════ -->
<script>
(function() {
  const overlay  = document.getElementById('xtm-search-overlay');
  const input    = document.getElementById('xtm-search-input');
  const results  = document.getElementById('xtm-search-results');
  const btnOpen  = document.getElementById('btn-open-search');
  const btnClose = document.getElementById('btn-close-search');
  if (!overlay) return;

  let timer;

  function openSearch() {
    overlay.classList.add('show');
    setTimeout(() => input && input.focus(), 80);
  }
  function closeSearch() {
    overlay.classList.remove('show');
    if (input) { input.value = ''; }
    if (results) results.innerHTML = `
      <div class="p-4 text-center text-muted" style="font-size:13px;">
        <i class="bi bi-search d-block fs-4 mb-2 opacity-25"></i>
        Ketik minimal 2 karakter untuk mencari…
      </div>`;
  }

  btnOpen && btnOpen.addEventListener('click', function(e){ e.preventDefault(); openSearch(); });
  btnClose && btnClose.addEventListener('click', closeSearch);
  overlay.addEventListener('click', function(e){ if (e.target === overlay) closeSearch(); });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeSearch(); });

  input && input.addEventListener('input', function() {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) {
      results.innerHTML = `<div class="p-4 text-center text-muted" style="font-size:13px;"><i class="bi bi-search d-block fs-4 mb-2 opacity-25"></i>Ketik minimal 2 karakter…</div>`;
      return;
    }
    results.innerHTML = `<div class="p-3 text-center text-muted" style="font-size:13px;"><div class="spinner-border spinner-border-sm me-2"></div>Mencari…</div>`;
    timer = setTimeout(() => {
      fetch('ajax/search.php?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
          if (!data.length) {
            results.innerHTML = `<div class="p-4 text-center text-muted" style="font-size:13px;"><i class="bi bi-search d-block fs-4 mb-2 opacity-25"></i>Tidak ada hasil untuk "<b>${q}</b>"</div>`;
            return;
          }
          const typeColors = {
            Berita:'#3B82F6', Galeri:'#8B5CF6', Guru:'#22C55E',
            Kontak:'#F97316', Profil:'#06B6D4'
          };
          results.innerHTML = data.map(item => {
            const color = typeColors[item.type] || '#64748B';
            return `<a href="${item.url}" class="xtm-search-result" style="display:flex;">
              <span style="background:${color}18;color:${color};padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;flex-shrink:0;align-self:flex-start;margin-top:2px;">${item.type}</span>
              <div style="flex:1;padding-left:10px;">
                <div style="font-weight:600;font-size:14px;color:#0F172A;">${item.label}</div>
                ${item.sub ? `<div style="font-size:12px;color:#64748B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:400px;">${item.sub}</div>` : ''}
              </div>
              <i class="bi bi-chevron-right text-muted ms-2 align-self-center"></i>
            </a>`;
          }).join('');
        })
        .catch(() => {
          results.innerHTML = `<div class="p-4 text-center text-danger" style="font-size:13px;">Gagal memuat hasil pencarian.</div>`;
        });
    }, 300);
  });
})();
</script>

<!-- ════════════════════════════════════════
     CHAT AUTO-SCROLL (untuk pesan.php)
════════════════════════════════════════ -->
<script>
  const chatBody = document.getElementById('chat-messages');
  if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
</script>

<!-- ════════════════════════════════════════
     CHART INIT (untuk dashboard — hanya jika ada elemen)
════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Chart berita per bulan
  if (document.querySelector('#chart-berita')) {
    new ApexCharts(document.querySelector('#chart-berita'), {
      series: [{ name: 'Berita', data: window.chartBeritaData || [] }],
      chart: { type: 'area', height: 200, toolbar: { show: false }, sparkline: { enabled: false } },
      colors: ['#3B82F6'],
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2 },
      fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
      xaxis: { categories: window.chartBeritaLabels || [], labels: { style: { fontSize: '11px' } } },
      yaxis: { min: 0, labels: { style: { fontSize: '11px' } } },
      grid: { borderColor: '#F1F5F9' },
      tooltip: { theme: 'light' },
    }).render();
  }
});
</script>

</body>
</html>