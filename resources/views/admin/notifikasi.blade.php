<!doctype html>
<html lang="id">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifikasi Admin</title>

  <style>
    :root{
      --orange:#e5861f;
      --orangeSoft:#fff4e6;
      --bg:#f6f6f6;
      --card:#ffffff;
      --border:#eeeeee;
      --text:#222;
      --muted:#777;
      --green:#22c55e;
      --red:#ef4444;
      --topbarH:60px;
      --sidebarW:220px;
    }

    *{ box-sizing:border-box; }

    html, body{
      margin:0;
      font-family:Arial, sans-serif;
      background:var(--bg);
      color:var(--text);
      overflow-x:hidden;
    }

    .topbar{
      position:fixed;
      top:0; left:0; right:0;
      height:var(--topbarH);
      z-index:9999;
      background:var(--orange);
      color:#fff;
      padding:14px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
    }

    .topbar-right{
      display:flex;
      gap:12px;
      align-items:center;
    }

    .notif-icon{
  position:relative;
  width:36px;
  height:36px;
  border-radius:50%;
  background:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:18px;
  text-decoration:none;
  color:#e5861f;
  box-shadow:0 4px 10px rgba(0,0,0,.12);
}

.notif-count{
  position:absolute;
  top:-6px;
  right:-6px;
  min-width:20px;
  height:20px;
  padding:0 6px;
  border-radius:999px;
  background:#ef4444;
  color:#fff;
  font-size:11px;
  font-weight:800;
  display:flex;
  align-items:center;
  justify-content:center;
  border:2px solid #fff;
}

    .logout-btn{
      background:#fff4e6;
      color:#e5861f;
      border:1px solid rgba(229,134,31,.4);
      padding:8px 14px;
      border-radius:10px;
      cursor:pointer;
      font-weight:700;
    }

    .sidebar{
      position:fixed;
      top:var(--topbarH);
      left:0;
      width:var(--sidebarW);
      height:calc(100vh - var(--topbarH));
      overflow-y:auto;
      background:#fff;
      border-right:1px solid var(--border);
      padding:14px;
      z-index:9998;
    }

    .sidebar a{
      display:block;
      padding:10px 10px;
      color:var(--orange);
      text-decoration:none;
      border-radius:8px;
      margin-bottom:6px;
      font-size:14px;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }

    .sidebar a.active{
      background:var(--orangeSoft);
      font-weight:700;
    }

    .content{
      padding:18px;
      padding-top:calc(var(--topbarH) + 18px);
      margin-left:var(--sidebarW);
      min-height:100vh;
    }

    .card{
      background:#fff;
      border-radius:14px;
      padding:18px;
      border:1px solid var(--border);
    }

    .page-head{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom:16px;
    }

    .page-title{
      margin:0;
      font-size:20px;
      font-weight:800;
    }

    .page-subtitle{
      margin-top:4px;
      font-size:13px;
      color:#777;
    }

    .badge-wait{
      background:#fff4e6;
      color:#b45309;
      padding:8px 12px;
      border-radius:999px;
      font-size:13px;
      font-weight:800;
    }

    .empty-box{
      padding:18px;
      border-radius:12px;
      background:#fff4e6;
      color:#7a4b10;
      border:1px solid rgba(229,134,31,.25);
      font-size:14px;
    }

    .notif-list{
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));
      gap:14px;
    }

    .notif-card{
      background:#fffaf3;
      border:1px solid #f0b36a;
      border-radius:16px;
      padding:16px;
    }

    .notif-top{
      display:flex;
      justify-content:space-between;
      gap:10px;
      align-items:flex-start;
      margin-bottom:10px;
    }

    .mk-title{
      font-size:15px;
      font-weight:800;
      line-height:1.3;
    }

    .status-pill{
      background:#fff;
      color:#b45309;
      border:1px solid #f0b36a;
      padding:5px 9px;
      border-radius:999px;
      font-size:11px;
      font-weight:800;
      white-space:nowrap;
    }

    .info{
      font-size:13px;
      color:#444;
      line-height:1.7;
    }

    .reason{
      margin-top:12px;
      padding:12px;
      background:#fff;
      border:1px solid #f5d4ad;
      border-radius:12px;
      font-size:13px;
      color:#444;
      line-height:1.5;
    }

    .actions{
      display:flex;
      gap:8px;
      margin-top:14px;
      flex-wrap:wrap;
    }

    .btn{
      border:0;
      padding:10px 14px;
      border-radius:10px;
      cursor:pointer;
      font-weight:800;
      font-size:13px;
      text-decoration:none;
      display:inline-block;
    }

    .btn-approve{
      background:var(--green);
      color:#fff;
    }

    .btn-reject{
      background:var(--red);
      color:#fff;
    }

    .btn-back{
      background:var(--orangeSoft);
      color:var(--orange);
      border:1px solid rgba(229,134,31,.35);
    }

    .modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.45);
  z-index:10000;
}

.modal-content{
  background:#fff;
  width:420px;
  max-width:90%;
  margin:90px auto;
  padding:20px;
  border-radius:16px;
}

.modal-content h3{
  margin-top:0;
  margin-bottom:14px;
}

.modal-content label{
  display:block;
  font-size:13px;
  font-weight:700;
  margin-bottom:8px;
}

.modal-content textarea{
  width:100%;
  padding:12px;
  border:1px solid #ddd;
  border-radius:12px;
  resize:vertical;
  font-family:Arial, sans-serif;
}

.modal-actions{
  display:flex;
  gap:8px;
  margin-top:14px;
  justify-content:flex-end;
}

.riwayat-card.disetujui{
  background:rgba(34,197,94,.08);
  border:1.5px solid #22c55e;
}

.riwayat-card.ditolak{
  background:rgba(239,68,68,.08);
  border:1.5px solid #ef4444;
}

.filter-riwayat{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
  margin-bottom:14px;
}

.filter-btn{
  border:1px solid #f0b36a;
  background:#fff4e6;
  color:#b45309;
  padding:8px 12px;
  border-radius:10px;
  font-weight:800;
  cursor:pointer;
}

.filter-btn.active{
  background:#e5861f;
  color:#fff;
}

.load-more-wrap{
  margin-top:16px;
  text-align:center;
}

.btn-load-more{
  background:#fff4e6;
  color:#b45309;
  border:1px solid #f0b36a;
  padding:10px 16px;
  border-radius:12px;
  font-weight:800;
  cursor:pointer;
}
  </style>
</head>

<body>
  <div class="topbar">
    <div><b>Web Admin Penjadwalan Perkuliahan</b></div>

    <div class="topbar-right">
      <a class="notif-icon" href="{{ route('admin.notifikasi') }}" title="Permohonan perubahan jadwal">
  🔔
  @if(($jumlahPermohonanMenunggu ?? 0) > 0)
    <span class="notif-count">{{ $jumlahPermohonanMenunggu }}</span>
  @endif
</a>

      <span>Admin</span>

      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout-btn" type="submit">Logout</button>
      </form>
    </div>
  </div>

  <div class="sidebar">
    <a href="{{ route('admin.monitoring') }}">Monitoring Jadwal</a>
    <a href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
    <a href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
    <a class="active" href="{{ route('admin.notifikasi') }}">Notifikasi</a>
  </div>

  <div class="content">
    @if(session('success'))
  <div style="
    margin-bottom:14px;
    background:#d4edda;
    color:#155724;
    padding:12px;
    border-radius:10px;
    border:1px solid #c3e6cb;
    font-size:14px;
  ">
    {{ session('success') }}
  </div>
@endif
    <div class="card">
      <div class="page-head">
        <div>
          <h3 class="page-title">Notifikasi Permohonan Jadwal</h3>
          <div class="page-subtitle">
            Daftar permohonan perubahan jadwal dari dosen yang menunggu persetujuan admin.
          </div>
        </div>

        <div class="badge-wait">
          {{ $jumlahPermohonanMenunggu }} Menunggu
        </div>
      </div>

      @if($permohonanMenunggu->isEmpty())
        <div class="empty-box">
          Belum ada permohonan perubahan jadwal dari dosen.
        </div>
      @else
        <div class="notif-list">
          @foreach($permohonanMenunggu as $p)
            @php
              $jadwal = $p->jadwal;
              $pengampu = optional($jadwal)->pengampu;
              $mk = optional(optional($pengampu)->mataKuliah)->nama_mk ?? '-';

              $dosen1 = optional(optional($pengampu)->dosen)->nama;
              $dosen2 = optional(optional($pengampu)->dosen2)->nama;
              $namaDosen = $dosen2 ? $dosen1 . ' / ' . $dosen2 : ($dosen1 ?? '-');

              $waktu = optional($jadwal)->waktu;
              $ruangan = optional($jadwal)->ruangan;
            @endphp

            <div class="notif-card">
              <div class="notif-top">
                <div class="mk-title">{{ $mk }}</div>
                <div class="status-pill">MENUNGGU</div>
              </div>

              <div class="info">
                <div><b>Dosen:</b> {{ $namaDosen }}</div>
                <div><b>Kelas:</b> {{ optional($jadwal)->kelas ?? '-' }}</div>
                <div><b>Pertemuan:</b> {{ $p->pertemuan_ke }}</div>
                <div>
                  <b>Jadwal Lama:</b>
                  {{ optional($waktu)->hari ?? '-' }},
                  {{ $waktu ? \Carbon\Carbon::parse($waktu->jam_mulai)->format('H:i') : '-' }}
                  -
                  {{ $waktu ? \Carbon\Carbon::parse($waktu->jam_selesai)->format('H:i') : '-' }}
                </div>
                <div><b>Ruangan:</b> {{ optional($ruangan)->kode_ruangan ?? '-' }}</div>
              </div>

              <div class="reason">
                <b>Alasan Dosen:</b><br>
                {{ $p->alasan_batal ?? '-' }}
              </div>

              <div class="actions">
                <a class="btn btn-approve" href="{{ route('admin.notifikasi.setujui', $p->id) }}">
                     Setujui
                </a>

               <button class="btn btn-reject" type="button" onclick="openTolakModal({{ $p->id }})">
                Tolak
                </button>

              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>


    {{-- RIWAYAT PERMOHONAN --}}
    <div class="card" style="margin-top:16px;">
      <div class="page-head">
        <div>
          <h3 class="page-title">Riwayat Permohonan Jadwal</h3>
          <div class="page-subtitle">
            Rekap permohonan perubahan jadwal yang sudah ditolak atau disetujui admin.
          </div>
        </div>
      </div>

            <div class="filter-riwayat">
        <button type="button" class="filter-btn active" onclick="filterRiwayat('semua', this)">
          Semua
        </button>
        <button type="button" class="filter-btn" onclick="filterRiwayat('disetujui', this)">
          Disetujui
        </button>
        <button type="button" class="filter-btn" onclick="filterRiwayat('ditolak', this)">
          Ditolak
        </button>
      </div>

      @if($riwayatPermohonan->isEmpty())
        <div class="empty-box">
          Belum ada riwayat permohonan jadwal.
        </div>
      @else
        <div class="notif-list">
          @foreach($riwayatPermohonan as $r)
            @php
              $jadwal = $r->jadwal;
              $pengampu = optional($jadwal)->pengampu;
              $mk = optional(optional($pengampu)->mataKuliah)->nama_mk ?? '-';

              $dosen1 = optional(optional($pengampu)->dosen)->nama;
              $dosen2 = optional(optional($pengampu)->dosen2)->nama;
              $namaDosen = $dosen2 ? $dosen1 . ' / ' . $dosen2 : ($dosen1 ?? '-');

              $waktuLama = optional($jadwal)->waktu;
              $ruanganLama = optional($jadwal)->ruangan;

              $waktuBaru = $r->waktu;
              $ruanganBaru = $r->ruangan;
            @endphp

            <div class="notif-card riwayat-card {{ $r->status === 'ditolak' ? 'ditolak' : 'disetujui' }}">
              <div class="notif-top">
                <div class="mk-title">{{ $mk }}</div>

                @if($r->status === 'ditolak')
                  <div class="status-pill" style="color:#ef4444; border-color:#ef4444;">
                    DITOLAK
                  </div>
                @else
                  <div class="status-pill" style="color:#22c55e; border-color:#22c55e;">
                    DISETUJUI
                  </div>
                @endif
              </div>

              <div class="info">
                <div><b>Dosen:</b> {{ $namaDosen }}</div>
                <div><b>Kelas:</b> {{ optional($jadwal)->kelas ?? '-' }}</div>
                <div><b>Pertemuan:</b> {{ $r->pertemuan_ke }}</div>

                <div>
                  <b>Jadwal Lama:</b>
                  {{ optional($waktuLama)->hari ?? '-' }},
                  {{ $waktuLama ? \Carbon\Carbon::parse($waktuLama->jam_mulai)->format('H:i') : '-' }}
                  -
                  {{ $waktuLama ? \Carbon\Carbon::parse($waktuLama->jam_selesai)->format('H:i') : '-' }},
                  Ruang {{ optional($ruanganLama)->kode_ruangan ?? '-' }}
                </div>

                @if($r->status === 'pindah')
                  <div>
                    <b>Jadwal Baru:</b>
                    {{ optional($waktuBaru)->hari ?? '-' }},
                    {{ $waktuBaru ? \Carbon\Carbon::parse($waktuBaru->jam_mulai)->format('H:i') : '-' }}
                    -
                    {{ $waktuBaru ? \Carbon\Carbon::parse($waktuBaru->jam_selesai)->format('H:i') : '-' }},
                    Ruang {{ optional($ruanganBaru)->kode_ruangan ?? '-' }}
                  </div>
                @endif
              </div>

              <div class="reason">
                <b>Alasan Dosen:</b><br>
                {{ $r->alasan_batal ?? '-' }}

                @if($r->status === 'ditolak')
                  <hr style="border:0; border-top:1px solid #eee; margin:10px 0;">
                  <b>Alasan Penolakan Admin:</b><br>
                  {{ $r->alasan_tolak ?? '-' }}
                @endif
              </div>
            </div>
                   @endforeach
        </div>

        <div class="load-more-wrap">
          <button
              type="button"
              class="btn-load-more"
              id="btnLoadMoreRiwayat"
              onclick="loadMoreRiwayat()">
              Tampilkan lainnya
          </button>
        </div>

      @endif
    </div>
<div id="modalTolak" class="modal">
  <div class="modal-content">
    <h3>Tolak Permohonan</h3>

    <form id="formTolak" method="POST">
      @csrf

      <label>Alasan Penolakan</label>
      <textarea
        name="alasan_tolak"
        required
        rows="4"
        placeholder="Masukkan alasan penolakan..."
      ></textarea>

      <div class="modal-actions">
        <button type="submit" class="btn btn-reject">
          Kirim Penolakan
        </button>

        <button type="button" class="btn btn-back" onclick="closeTolakModal()">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>
<script>
function openTolakModal(id) {
  const modal = document.getElementById('modalTolak');
  const form = document.getElementById('formTolak');

  form.action = `/admin/notifikasi/${id}/tolak`;
  modal.style.display = 'block';
}

function closeTolakModal() {
  document.getElementById('modalTolak').style.display = 'none';
}

window.onclick = function(event) {
  const modal = document.getElementById('modalTolak');

  if (event.target === modal) {
    closeTolakModal();
  }
}

let riwayatVisible = 8;
let riwayatFilter = 'semua';

function applyRiwayatView() {
  const cards = document.querySelectorAll('.riwayat-card');
  let shown = 0;
  let totalMatch = 0;

  cards.forEach(card => {
    const cocokFilter =
      riwayatFilter === 'semua' ||
      card.classList.contains(riwayatFilter);

    if (!cocokFilter) {
      card.style.display = 'none';
      return;
    }

    totalMatch++;

    if (shown < riwayatVisible) {
      card.style.display = 'block';
      shown++;
    } else {
      card.style.display = 'none';
    }
  });

  const btn = document.getElementById('btnLoadMoreRiwayat');
  if (btn) {
    btn.style.display = totalMatch > riwayatVisible ? 'inline-block' : 'none';
  }
}

function filterRiwayat(filter, btn) {
  riwayatFilter = filter;
  riwayatVisible = 8;

  document.querySelectorAll('.filter-btn').forEach(b => {
    b.classList.remove('active');
  });

  btn.classList.add('active');
  applyRiwayatView();
}

function loadMoreRiwayat() {
  riwayatVisible += 8;
  applyRiwayatView();
}

document.addEventListener('DOMContentLoaded', applyRiwayatView);
</script>
</body>
</html>