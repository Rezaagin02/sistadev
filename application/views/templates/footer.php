  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

</body>

<?php
  // normalize flash from either old keys or new helper
  $type = $this->session->flashdata('alert_type');
  $msg  = $this->session->flashdata('alert_msg');

  // fallback to legacy keys
  if (!$type && $this->session->flashdata('success')) { $type='success'; $msg=$this->session->flashdata('success'); }
  if (!$type && $this->session->flashdata('error'))   { $type='error';   $msg=$this->session->flashdata('error'); }
  if (!$type && $this->session->flashdata('info'))    { $type='info';    $msg=$this->session->flashdata('info'); }
  if (!$type && $this->session->flashdata('warning')) { $type='warning'; $msg=$this->session->flashdata('warning'); }
?>

<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true"
     data-type="<?= htmlspecialchars($type ?? '', ENT_QUOTES) ?>"
     data-message="<?= htmlspecialchars($msg  ?? '', ENT_QUOTES) ?>">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
      <div class="alert-head p-3 d-flex align-items-center gap-2">
        <i class="bi" id="alertIcon" style="font-size:20px"></i>
        <strong class="m-0" id="alertTitle">Notification</strong>
      </div>
      <div class="modal-body">
        <p class="m-0" id="alertText">—</p>
      </div>
      <div class="px-3 pb-3">
        <div class="progress" id="alertProgress" style="height:3px;">
          <div class="progress-bar" role="progressbar" style="width: 100%;"></div>
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
  /* header variants */
  .alert-head.success{ background:#ecfdf5; color:#065f46; }   /* green */
  .alert-head.error{   background:#fef2f2; color:#991b1b; }   /* red */
  .alert-head.info{    background:#eff6ff; color:#1d4ed8; }   /* blue */
  .alert-head.warning{ background:#fffbeb; color:#92400e; }   /* amber */

  /* progress color follow header */
  .alert-head.success + .modal-body + .px-3 .progress-bar{ background:#34d399; }
  .alert-head.error   + .modal-body + .px-3 .progress-bar{ background:#f87171; }
  .alert-head.info    + .modal-body + .px-3 .progress-bar{ background:#60a5fa; }
  .alert-head.warning + .modal-body + .px-3 .progress-bar{ background:#fbbf24; }
</style>

<script>
(function(){
  const el = document.getElementById('alertModal');
  if (!el) return;

  const type = (el.dataset.type || '').toLowerCase();
  const msg  = (el.dataset.message || '').trim();
  if (!type || !msg) return; // nothing to show

  // map type -> icon & title
  const map = {
    success: { icon:'bi-check-circle-fill', title:'Berhasil' },
    error:   { icon:'bi-x-circle-fill',     title:'Gagal'    },
    info:    { icon:'bi-info-circle-fill',  title:'Info'     },
    warning: { icon:'bi-exclamation-triangle-fill', title:'Perhatian' }
  };
  const conf = map[type] || map.info;

  // fill UI
  document.getElementById('alertIcon').className = 'bi ' + conf.icon;
  document.getElementById('alertTitle').textContent = conf.title;
  document.getElementById('alertText').textContent  = msg;

  const head = el.querySelector('.alert-head');
  head.classList.remove('success','error','info','warning');
  head.classList.add(type);

  // show modal
  const modal = new bootstrap.Modal(el, { backdrop:'static', keyboard:true });
  modal.show();

  // auto close with progress countdown (2.5s)
  const bar = el.querySelector('#alertProgress .progress-bar');
  let d = 2500, step = 10, left = d;
  const t = setInterval(()=>{
    left -= step;
    const pct = Math.max(0, (left/d)*100);
    bar.style.width = pct + '%';
    if (left <= 0) { clearInterval(t); modal.hide(); }
  }, step);

  // if user closes early, stop timer
  el.addEventListener('hidden.bs.modal', () => clearInterval(t), { once:true });
})();
</script>

</html>


