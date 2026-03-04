<?php
// JANGAN panggil session_start() di sini — sudah ada di header.php
include "template/header.php"; // header.php sudah handle session + $koneksi
include "template/menu.php";
include "template/avatar_helper.php";

// $koneksi sudah tersedia dari header.php

// Ambil ID admin login
$username_login = $_SESSION['username'];
$r_me = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='" . mysqli_real_escape_string($koneksi, $username_login) . "' LIMIT 1");
$me   = mysqli_fetch_assoc($r_me);
$my_id = (int)($me['id'] ?? 0);
$_SESSION['id_admin'] = $my_id;

// Daftar admin lain
$r_admins = mysqli_query($koneksi, "SELECT id, nama_admin, username FROM admin WHERE id != $my_id ORDER BY nama_admin");
$admins   = [];
while ($d = mysqli_fetch_assoc($r_admins)) $admins[] = $d;

// Cek tabel pesan_admin
$tbl_exists = mysqli_num_rows(mysqli_query($koneksi, "SHOW TABLES LIKE 'pesan_admin'")) > 0;

// Partner yang dipilih
$partner_id = isset($_GET['u']) ? (int)$_GET['u'] : 0;
$partner    = null;
if ($partner_id) {
    $r_p    = mysqli_query($koneksi, "SELECT * FROM admin WHERE id=$partner_id LIMIT 1");
    $partner = mysqli_fetch_assoc($r_p);
}
?>
<main class="app-main" style="height:calc(100vh - 64px);overflow:hidden;">
  <div style="display:flex;height:100%;font-family:'Outfit',sans-serif;">

    <!-- ══ LEFT: Admin list ════════════════════════════════ -->
    <div style="width:280px;border-right:1px solid #F1F5F9;display:flex;flex-direction:column;background:#fff;flex-shrink:0;">

      <!-- Header -->
      <div style="padding:16px;border-bottom:1px solid #F1F5F9;">
        <h5 class="mb-2 fw-bold" style="font-family:'Outfit',sans-serif;color:#0F172A;">💬 Pesan</h5>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
          <input type="text" id="search-admin" class="form-control bg-light border-0" placeholder="Cari admin…" style="font-size:13px;">
        </div>
      </div>

      <!-- List -->
      <div style="flex:1;overflow-y:auto;" id="admin-list">
        <?php if (empty($admins)): ?>
          <div class="p-4 text-center text-muted" style="font-size:13px;">
            <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
            Tidak ada admin lain.
          </div>
        <?php else: ?>
          <?php foreach ($admins as $adm):
            // Last message
            $last_msg = '';
            $last_time = '';
            $unread_cnt = 0;
            if ($tbl_exists) {
              $r_lm = mysqli_query($koneksi,
                "SELECT pesan, created_at FROM pesan_admin
                 WHERE (pengirim_id=$my_id AND penerima_id={$adm['id']})
                    OR (pengirim_id={$adm['id']} AND penerima_id=$my_id)
                 ORDER BY created_at DESC LIMIT 1");
              $lm = mysqli_fetch_assoc($r_lm);
              $last_msg  = $lm ? $lm['pesan'] : '';
              $last_time = $lm ? date('H:i', strtotime($lm['created_at'])) : '';

              $r_cnt = mysqli_query($koneksi,
                "SELECT COUNT(*) c FROM pesan_admin WHERE penerima_id=$my_id AND pengirim_id={$adm['id']} AND dibaca=0");
              $unread_cnt = (int)(mysqli_fetch_assoc($r_cnt)['c'] ?? 0);
            }
            $is_active = ($partner_id === (int)$adm['id']);
          ?>
          <a href="pesan.php?u=<?= $adm['id'] ?>"
             class="d-flex align-items-center gap-3 px-3 py-2 text-decoration-none admin-item"
             style="border-left:3px solid <?= $is_active?'#3B82F6':'transparent' ?>;background:<?= $is_active?'#EFF6FF':'transparent' ?>;transition:all .12s;"
             data-name="<?= strtolower(htmlspecialchars($adm['nama_admin'])) ?>">

            <div style="position:relative;">
              <?= render_avatar($adm['nama_admin'], 44) ?>
              <div style="position:absolute;bottom:2px;right:2px;width:11px;height:11px;background:#22C55E;border-radius:50%;border:2px solid #fff;"></div>
            </div>

            <div style="flex:1;overflow:hidden;">
              <div style="font-weight:700;font-size:13px;color:#0F172A;"><?= htmlspecialchars($adm['nama_admin']) ?></div>
              <div style="font-size:11px;color:#94A3B8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">
                <?= $last_msg ? htmlspecialchars($last_msg) : '<em>Mulai percakapan…</em>' ?>
              </div>
            </div>

            <div style="text-align:right;flex-shrink:0;">
              <?php if ($last_time): ?>
                <div style="font-size:10px;color:#94A3B8;"><?= $last_time ?></div>
              <?php endif; ?>
              <?php if ($unread_cnt > 0): ?>
                <span class="badge text-bg-danger" style="font-size:9px;"><?= $unread_cnt ?></span>
              <?php endif; ?>
            </div>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <!-- ══ END LEFT ══ -->


    <!-- ══ RIGHT: Chat area ═══════════════════════════════ -->
    <div style="flex:1;display:flex;flex-direction:column;background:#F8FAFC;min-width:0;">

      <?php if ($partner): ?>

        <!-- Chat header -->
        <div style="padding:14px 20px;background:#fff;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;gap:12px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
          <div style="position:relative;">
            <?= render_avatar($partner['nama_admin'], 42) ?>
            <div style="position:absolute;bottom:2px;right:2px;width:10px;height:10px;background:#22C55E;border-radius:50%;border:2px solid #fff;"></div>
          </div>
          <div>
            <div style="font-weight:700;font-size:15px;color:#0F172A;"><?= htmlspecialchars($partner['nama_admin']) ?></div>
            <div style="font-size:12px;color:#22C55E;font-weight:600;">● Online</div>
          </div>
          <div class="ms-auto">
            <span style="font-size:12px;color:#94A3B8;background:#F1F5F9;padding:4px 10px;border-radius:20px;">
              @<?= htmlspecialchars($partner['username']) ?>
            </span>
          </div>
        </div>

        <!-- Messages area -->
        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:20px 24px;display:flex;flex-direction:column;gap:6px;">

          <?php if (!$tbl_exists): ?>
            <div class="alert alert-warning mx-auto" style="max-width:460px;border-radius:12px;font-size:13px;">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              Tabel <code>pesan_admin</code> belum ada. Jalankan file <code>pesan_admin.sql</code> terlebih dahulu.
            </div>
          <?php else: ?>
            <?php
              // Load existing messages
              mysqli_query($koneksi, "UPDATE pesan_admin SET dibaca=1 WHERE penerima_id=$my_id AND pengirim_id=$partner_id AND dibaca=0");
              $r_msgs = mysqli_query($koneksi,
                "SELECT p.*, a.nama_admin FROM pesan_admin p
                 JOIN admin a ON a.id=p.pengirim_id
                 WHERE (p.pengirim_id=$my_id AND p.penerima_id=$partner_id)
                    OR (p.pengirim_id=$partner_id AND p.penerima_id=$my_id)
                 ORDER BY p.created_at ASC LIMIT 100");
              $prev_date = '';
              while ($msg = mysqli_fetch_assoc($r_msgs)):
                $is_me   = ((int)$msg['pengirim_id'] === $my_id);
                $msg_date = date('d F Y', strtotime($msg['created_at']));
                $msg_time = date('H:i', strtotime($msg['created_at']));
                if ($msg_date !== $prev_date):
                  $prev_date = $msg_date;
            ?>
              <div style="text-align:center;margin:10px 0;">
                <span style="background:#E2E8F0;color:#64748B;font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;">
                  <?= $msg_date ?>
                </span>
              </div>
            <?php endif; ?>
            <div style="display:flex;flex-direction:column;align-items:<?= $is_me?'flex-end':'flex-start' ?>;margin-bottom:4px;">
              <div style="max-width:70%;background:<?= $is_me?'linear-gradient(135deg,#2563EB,#1D4ED8)':'#fff' ?>;color:<?= $is_me?'#fff':'#1E293B' ?>;border-radius:<?= $is_me?'16px 16px 4px 16px':'16px 16px 16px 4px' ?>;padding:10px 14px;font-size:14px;line-height:1.5;box-shadow:0 1px 3px rgba(0,0,0,.08);">
                <?= nl2br(htmlspecialchars($msg['pesan'])) ?>
              </div>
              <div style="font-size:10px;color:#94A3B8;margin-top:3px;padding:0 4px;">
                <?= $msg_time ?>
                <?php if ($is_me && $msg['dibaca']): ?>
                  <i class="bi bi-check2-all ms-1" style="color:#22C55E;"></i>
                <?php elseif ($is_me): ?>
                  <i class="bi bi-check2 ms-1"></i>
                <?php endif; ?>
              </div>
            </div>
            <?php endwhile; ?>
          <?php endif; ?>

          <div id="chat-bottom"></div>
        </div>

        <!-- Input bar -->
        <div style="padding:14px 20px;background:#fff;border-top:1px solid #F1F5F9;">
          <form id="chat-form" style="display:flex;gap:10px;align-items:flex-end;" onsubmit="return false;">
            <div style="flex:1;position:relative;">
              <textarea id="chat-input"
                rows="1"
                placeholder="Kirim pesan ke <?= htmlspecialchars($partner['nama_admin']) ?>…"
                style="width:100%;border:1.5px solid #E2E8F0;border-radius:12px;padding:10px 44px 10px 14px;font-family:'Outfit',sans-serif;font-size:14px;resize:none;outline:none;line-height:1.5;max-height:120px;overflow-y:auto;transition:border .15s;"
                onfocus="this.style.borderColor='#3B82F6'"
                onblur="this.style.borderColor='#E2E8F0'"
              ></textarea>
            </div>
            <button type="submit" id="btn-send"
              style="width:44px;height:44px;border-radius:12px;border:none;background:linear-gradient(135deg,#2563EB,#1D4ED8);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(37,99,235,.3);flex-shrink:0;transition:opacity .15s;">
              <i class="bi bi-send-fill" style="font-size:16px;"></i>
            </button>
          </form>
          <div style="font-size:11px;color:#94A3B8;margin-top:6px;padding-left:2px;">
            Tekan <kbd>Enter</kbd> untuk kirim · <kbd>Shift+Enter</kbd> baris baru
          </div>
        </div>

        <!-- Chat JS -->
        <script>
        (function(){
          const MY_ID      = <?= $my_id ?>;
          const PARTNER_ID = <?= $partner_id ?>;
          const form       = document.getElementById('chat-form');
          const input      = document.getElementById('chat-input');
          const bottom     = document.getElementById('chat-bottom');
          let lastId       = <?php
            $r_lid = mysqli_query($koneksi, "SELECT MAX(id) mid FROM pesan_admin WHERE (pengirim_id=$my_id AND penerima_id=$partner_id) OR (pengirim_id=$partner_id AND penerima_id=$my_id)");
            echo (int)(mysqli_fetch_assoc($r_lid)['mid'] ?? 0);
          ?>;

          function scrollBottom() {
            if (bottom) bottom.scrollIntoView({ behavior: 'smooth' });
          }
          scrollBottom();

          function appendMsg(msg) {
            const isMe = parseInt(msg.pengirim_id) === MY_ID;
            const date = new Date(msg.created_at);
            const timeStr = date.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
            const msgDiv  = document.createElement('div');
            msgDiv.style.cssText = `display:flex;flex-direction:column;align-items:${isMe?'flex-end':'flex-start'};margin-bottom:4px;`;
            msgDiv.innerHTML = `
              <div style="max-width:70%;background:${isMe?'linear-gradient(135deg,#2563EB,#1D4ED8)':'#fff'};color:${isMe?'#fff':'#1E293B'};border-radius:${isMe?'16px 16px 4px 16px':'16px 16px 16px 4px'};padding:10px 14px;font-size:14px;line-height:1.5;box-shadow:0 1px 3px rgba(0,0,0,.08);">
                ${msg.pesan.replace(/\n/g,'<br>')}
              </div>
              <div style="font-size:10px;color:#94A3B8;margin-top:3px;padding:0 4px;">
                ${timeStr}${isMe ? ' <i class="bi bi-check2" style="color:#94A3B8"></i>' : ''}
              </div>`;
            const container = document.getElementById('chat-messages');
            container.insertBefore(msgDiv, bottom);
            scrollBottom();
          }

          // Send
          function sendMsg() {
            const text = input.value.trim();
            if (!text) return;
            input.value = '';
            input.style.height = 'auto';

            fetch('ajax/pesan_handler.php', {
              method: 'POST',
              headers: {'Content-Type':'application/x-www-form-urlencoded'},
              body: `action=kirim&penerima_id=${PARTNER_ID}&pesan=${encodeURIComponent(text)}`
            }).then(r=>r.json()).then(data=>{
              if (data.ok) {
                appendMsg({ pengirim_id: MY_ID, penerima_id: PARTNER_ID, pesan: text, created_at: new Date().toISOString(), dibaca: 0 });
                lastId = data.id || lastId;
              }
            });
          }

          form.addEventListener('submit', sendMsg);
          input.addEventListener('keydown', function(e){
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); }
          });

          // Auto-grow textarea
          input.addEventListener('input', function(){
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
          });

          // Polling untuk pesan baru
          function poll() {
            fetch(`ajax/pesan_handler.php?action=ambil&partner_id=${PARTNER_ID}&last_id=${lastId}`)
              .then(r=>r.json())
              .then(msgs=>{
                if (msgs && msgs.length) {
                  msgs.forEach(m => {
                    if (parseInt(m.pengirim_id) !== MY_ID) {
                      appendMsg(m);
                    }
                    if (parseInt(m.id) > lastId) lastId = parseInt(m.id);
                  });
                }
              });
          }
          setInterval(poll, 3000);
        })();
        </script>

      <?php else: ?>

        <!-- No chat selected -->
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94A3B8;">
          <i class="bi bi-chat-dots" style="font-size:56px;margin-bottom:16px;opacity:.3;"></i>
          <div style="font-weight:600;font-size:16px;color:#64748B;">Pilih percakapan</div>
          <div style="font-size:13px;margin-top:6px;">Pilih admin dari daftar kiri untuk mulai chat</div>
        </div>

      <?php endif; ?>

    </div>
    <!-- ══ END RIGHT ══ -->

  </div>
</main>

<script>
// Filter admin list by name
document.getElementById('search-admin')?.addEventListener('input', function(){
  const q = this.value.toLowerCase();
  document.querySelectorAll('#admin-list .admin-item').forEach(el => {
    el.style.display = el.dataset.name.includes(q) ? '' : 'none';
  });
});
</script>

<?php include "template/footer.php"; ?>