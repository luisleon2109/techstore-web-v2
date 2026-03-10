// assets/js/app.js — Utilidades globales

// ── Toast ──
function toast(msg, type = 'success') {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.className = 'toast ' + type;
  t.classList.add('show');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove('show'), 3000);
}

// ── Modal ──
function openModal(id) {
  const el = document.getElementById(id);
  if (el) el.classList.add('open');
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) el.classList.remove('open');
}
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
  if (e.target.dataset.close) closeModal(e.target.dataset.close);
});

// ── AJAX helper ──
async function api(url, data = null, method = null) {
  const opts = {
    method: method || (data ? 'POST' : 'GET'),
    headers: { 'Content-Type': 'application/json' },
  };
  if (data) opts.body = JSON.stringify(data);
  const res = await fetch(url, opts);
  return res.json();
}

// ── Format currency ──
function fmt(n) {
  return 'Bs ' + Number(n).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ── Search filter helper ──
function filterTable(inputId, tableBodyId, cols) {
  const q = document.getElementById(inputId)?.value.toLowerCase() ?? '';
  document.querySelectorAll('#' + tableBodyId + ' tr').forEach(tr => {
    const text = [...tr.querySelectorAll('td')].map(td => td.textContent.toLowerCase()).join(' ');
    tr.style.display = text.includes(q) ? '' : 'none';
  });
}
