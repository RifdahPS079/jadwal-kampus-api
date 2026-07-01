<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Ruangan & Waktu</title>

  <style>
    :root{
      --orange:#e5861f;
      --orangeSoft:#fff4e6;
      --bg:#f6f6f6;
      --card:#ffffff;
      --border:#eeeeee;
      --text:#222;
      --muted:#777;
      --danger:#b00020;
      --dangerSoft:#ffe6e6;

      --topbarH: 60px;
      --sidebarW: 270px;
    }

    *{ box-sizing:border-box; }
    html, body{
      margin:0;
      font-family: Arial, sans-serif;
      background:var(--bg);
      color:var(--text);
      overflow-x:hidden;
    }

    /* TOPBAR FIXED */
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
    .topbar-right{ display:flex; gap:12px; align-items:center; }
    .badge{
      width:34px; height:34px;
      border-radius:50%;
      background:rgba(255,255,255,.22);
      display:flex; align-items:center; justify-content:center;
      font-weight:800;
    }

    .logout-btn{
      background:#fff4e6; /* ⬅️ background soft orange */
      color:#e5861f;      /* ⬅️ teks orange */
      border:1px solid rgba(229,134,31,.4);
      padding:8px 14px;
      border-radius:10px;
      cursor:pointer;
      font-weight:700;
      transition:0.2s;
    }

    /* SIDEBAR FIXED */
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

    /* CONTENT */
    .content{
      padding:18px;
      padding-top:calc(var(--topbarH) + 18px);
      margin-left:var(--sidebarW);
      min-height:100vh;
      min-width:0;
      overflow-x:hidden;
    }

    .card{
      background:var(--card);
      border-radius:12px;
      padding:16px;
      border:1px solid var(--border);
    }

    .page-title{ margin:0 0 12px; font-size:18px; }

    .grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:14px;
    }

    @media (max-width: 900px){
      :root{ --sidebarW:270px; }
      .grid{ grid-template-columns: 1fr; }
    }

    .panel{
      border:1px solid var(--border);
      border-radius:12px;
      padding:14px;
      background:#fff;
    }
    .panel h4{ margin:0 0 10px; font-size:14px; }

    .notice{
      margin:0 0 12px;
      padding:10px 12px;
      border-radius:10px;
      background:var(--orangeSoft);
      color:#7a4b10;
      font-size:13px;
      border:1px solid rgba(229,134,31,.25);
    }

    label{ display:block; font-size:12px; color:var(--muted); margin-bottom:6px; }

    input{
      width:100%;
      padding:10px 12px;
      border-radius:10px;
      border:1px solid #ddd;
      outline:none;
      background:#fff;
      font-size:14px;
    }
    input:focus{
      border-color:var(--orange);
      box-shadow:0 0 0 4px rgba(229,134,31,.12);
    }

    .row{ display:flex; gap:10px; flex-wrap:wrap; }
    .col{ flex:1; min-width:220px; }
    .actions{ display:flex; gap:10px; margin-top:12px; flex-wrap:wrap; }

    .btn{
      border:0;
      padding:10px 14px;
      border-radius:10px;
      cursor:pointer;
      font-weight:800;
      font-size:13px;
      display:inline-block;
      text-decoration:none;
      white-space:nowrap;
    }
    .btn-orange{
      background:var(--orange);
      color:#fff;
      box-shadow:0 10px 18px rgba(229,134,31,.22);
    }
    .btn-soft{
      background:var(--orangeSoft);
      color:var(--orange);
      border:1px solid rgba(229,134,31,.35);
    }
    .btn-danger{
      background:var(--dangerSoft);
      color:var(--danger);
      border:1px solid rgba(176,0,32,.25);
    }

    .success{
      background:#eaffea;
      border:1px solid #c7f0c7;
      color:#1b6b1b;
      padding:10px 12px;
      border-radius:10px;
      font-size:13px;
      margin:0 0 12px;
    }
    .small-err{ color:var(--danger); font-size:12px; margin-top:6px; }

    /* TABLE */
    .table-wrap{ width:100%; overflow-x:auto; overflow-y:hidden; }
    .minw{ min-width: 900px; }

    table{
      width:100%;
      border-collapse:collapse;
      margin-top:12px;
      overflow:hidden;
      border-radius:10px;
    }
    th, td{
      border:1px solid #ddd;
      padding:10px;
      vertical-align:middle;
      font-size:13px;
    }
    th{
      background:#f4f4f4;
      text-align:center;
      font-size:12px;
    }

    select{
  width:100%;
  padding:10px 12px;
  border-radius:10px;
  border:1px solid #ddd;
  outline:none;
  background:#fff;
  font-size:14px;
  height:42px; /* 🔥 ini penting biar sama tinggi */
}

select:focus{
  border-color:var(--orange);
  box-shadow:0 0 0 4px rgba(229,134,31,.12);
}
    .td-center{ text-align:center; }

.highlight-row{
    animation: highlightFade 4s ease;
}

@keyframes highlightFade{

    0%{
        background:#93c5fd;
    }

    100%{
        background:transparent;
    }

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

.bulk-bar{
  margin:14px 0;
  display:flex;
  justify-content:flex-end;
  align-items:center;
  gap:10px;
  flex-wrap:wrap;
}

.select-mode-info{
  font-size:13px;
  color:#777;
  font-weight:600;
}

.selected-count{
  display:none;
  padding:6px 10px;
  border-radius:8px;
  background:#fff4e6;
  color:#b45309;
  font-size:13px;
  font-weight:700;
}

.ruangan-check,
.waktu-check{
  display:none;
  width:18px;
  height:18px;
  cursor:pointer;
}

body.mode-pilih-ruangan .ruangan-check{
  display:inline-block;
}

body.mode-pilih-waktu .waktu-check{
  display:inline-block;
}

.btn-warning{
  background:#fff4e6;
  color:#b45309;
  border:1px solid #f0b36a;
}

.btn-grey{
  background:#eee;
  color:#555;
}

.aksi-wrap{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:8px;
  flex-wrap:nowrap;
}

.aksi-wrap form{
  margin:0;
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
      <!-- <div class="badge">A</div> -->
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout-btn" type="submit">Logout</button>
      </form>
    </div>
  </div>

  <div class="sidebar">
   <a href="{{ route('admin.monitoring') }}">Penyusunan & Monitoring Jadwal</a>
    <a href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a class="active" href="{{ route('admin.ruangan_waktu.index') }}">Ruangan & Waktu</a>
    <a href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
  </div>

  <div class="content">
    <div class="card">
      <h3 class="page-title">Kelolah Ruangan dan Waktu</h3>

      @if(session('ok'))
        <div class="success">{{ session('ok') }}</div>
      @endif

      <div class="grid">
        {{-- PANEL RUANGAN --}}
        <div class="panel">
          <h4>Tambah Ruangan</h4>

          @if(
              $errors->has('kode_ruangan')
              ||
              $errors->has('nama_ruangan')
          )

          <div style="
              background:#ffe6e6;
              color:red;
              padding:12px;
              border-radius:10px;
              margin-bottom:12px;
          ">

              @if($errors->has('kode_ruangan'))
                  <div>{{ $errors->first('kode_ruangan') }}</div>
              @endif

              @if($errors->has('nama_ruangan'))
                  <div>{{ $errors->first('nama_ruangan') }}</div>
              @endif

          </div>
          @endif
          <form method="POST" action="{{ route('admin.ruangan.store') }}">
            @csrf

            <div class="row">
              <div class="col">
                <label>Kode Ruangan</label>
                <input name="kode_ruangan" value="{{ old('kode_ruangan') }}" placeholder="Contoh: R.MACCA">

              </div>

              <div class="col">
                <label>Nama Ruangan</label>
                <input name="nama_ruangan" value="{{ old('nama_ruangan') }}" placeholder="Contoh: Ruang MACCA">
              
              </div>
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Gedung</label>
                <input name="gedung" value="{{ old('gedung') }}" placeholder="Contoh: Gedung Pemuda">
                @error('gedung') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="actions">
              <button class="btn btn-orange" type="submit">Simpan</button>
              <button class="btn btn-soft" type="reset">Reset</button>
            </div>
          </form>

          <hr style="border:none; border-top:1px solid var(--border); margin:14px 0;">

        </div>

        {{-- PANEL WAKTU --}}
        <div class="panel">
          <h4>Tambah Waktu</h4>
          @if($errors->has('waktu'))
          <div style="
              background:#ffe6e6;
              color:red;
              padding:14px;
              border-radius:12px;
              margin-bottom:14px;
              font-size:15px;
          ">
              {{ $errors->first('waktu') }}
          </div>
      @endif


          <form method="POST" action="{{ route('admin.waktu.store') }}">
            @csrf

            <div class="row">
              <div class="col">
                <label>Hari</label>
                <select name="hari" required>
                  <option value="">-- Pilih Hari --</option>
                  <option>Senin</option>
                  <option>Selasa</option>
                  <option>Rabu</option>
                  <option>Kamis</option>
                  <option>Jumat</option>
                  <option>Sabtu</option>
                  <option>Minggu</option>
                </select>
                @error('hari') <div class="small-err">{{ $message }}</div> @enderror
              </div>
              
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                @error('jam_mulai') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              <div class="col">
                <label>Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                @error('jam_selesai') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="actions">
              <button class="btn btn-orange" type="submit">Simpan</button>
              <button class="btn btn-soft" type="reset">Reset</button>
            </div>
          </form>

          <hr style="border:none; border-top:1px solid var(--border); margin:14px 0;">


        </div>
      </div>

      <div class="grid" style="margin-top:14px;">

  <div class="panel">
    <h4>Import Ruangan</h4>

    <div class="notice">
      Masukkan file Excel untuk import ruangan (xlsx/xls/csv)
    </div>

    <form method="POST" action="{{ route('admin.ruangan.import') }}" enctype="multipart/form-data">
      @csrf
      <label>Masukkan File Excel</label>
      <input type="file" name="file">

      <div class="actions">
        <button class="btn btn-orange">Import</button>
      </div>
    </form>

    <div style="font-size:12px; margin-top:10px;">
      <b>Format:</b> kode_ruangan, nama_ruangan, gedung
    </div>
  </div>

  <div class="panel">
    <h4>Import Waktu</h4>

    <div class="notice">
      Masukkan file Excel untuk import waktu (xlsx/xls/csv)
    </div>

    <form method="POST" action="{{ route('admin.waktu.import') }}" enctype="multipart/form-data">
      @csrf
      <label>Masukkan File Excel</label>
      <input type="file" name="file">

      <div class="actions">
        <button class="btn btn-orange">Import</button>
      </div>
    </form>

    <div style="font-size:12px; margin-top:10px;">
      <b>Format:</b> jam_mulai, jam_selesai
    </div>
  </div>

</div>

    </div>
      {{-- DAFTAR RUANGAN --}}
<div class="panel" style="margin-top:14px;">
  <h4>Daftar Ruangan</h4>

  <div class="bulk-bar">
    <div id="selectInfoRuangan" class="select-mode-info" style="display:none;">
      Pilih beberapa ruangan yang ingin dihapus
    </div>

    <div id="selectedCountRuangan" class="selected-count">
      Belum ada ruangan dipilih
    </div>

    <button type="button" id="btnModePilihRuangan" class="btn btn-soft" onclick="aktifkanModePilihRuangan()">
      ✅ Pilih Beberapa Ruangan
    </button>

    <button type="button" id="btnPilihSemuaRuangan" class="btn btn-warning" onclick="togglePilihSemuaRuangan()" style="display:none;">
      Pilih Semua
    </button>

    <button type="button" id="btnHapusTerpilihRuangan" class="btn btn-danger" onclick="hapusRuanganTerpilih()" style="display:none;">
      Hapus Terpilih
    </button>

    <button type="button" id="btnBatalPilihRuangan" class="btn btn-grey" onclick="batalModePilihRuangan()" style="display:none;">
      Batal
    </button>
  </div>

  <div class="table-wrap">
    <table class="minw">
      <thead>
        <tr>
          <th id="kolomPilihHeaderRuangan" style="width:45px; display:none;">Pilih</th>
          <th style="width:160px;">Kode Ruangan</th>
          <th>Nama Ruangan</th>
          <th style="width:220px;">Gedung</th>
          <th style="width:230px;">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @forelse($ruangans as $r)
          <tr
            id="ruangan-{{ $r->id }}"
            class="{{
              (
                session('highlight_ruangan_id') == $r->id
                ||
                in_array($r->id, session('highlight_ruangan_ids', []))
              )
              ? 'highlight-row'
              : ''
            }}"
          >
            <td class="td-center kolom-pilih-ruangan" style="display:none;">
              <input
                type="checkbox"
                class="ruangan-check"
                value="{{ $r->id }}"
                onclick="updateSelectedCountRuangan()"
              >
            </td>

            <td class="td-center">{{ $r->kode_ruangan ?? '-' }}</td>
            <td>{{ $r->nama_ruangan ?? '-' }}</td>
            <td>{{ $r->gedung ?? '-' }}</td>

            <td class="td-center">
              <div class="aksi-wrap">
                <a class="btn btn-soft" href="{{ route('admin.ruangan.edit', $r->id) }}">
                  Edit
                </a>

                <form method="POST"
                      action="{{ route('admin.ruangan.destroy', $r->id) }}"
                      onsubmit="return confirm('Yakin mau hapus ruangan ini?\n\n{{ $r->kode_ruangan }}');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="td-center" style="color:var(--muted); padding:16px;">
              Data ruangan belum ada.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

      {{-- DAFTAR WAKTU --}}
<div class="panel" style="margin-top:14px;">
  <h4>Daftar Waktu</h4>

  <div class="bulk-bar">
    <div id="selectInfoWaktu" class="select-mode-info" style="display:none;">
      Pilih beberapa waktu yang ingin dihapus
    </div>

    <div id="selectedCountWaktu" class="selected-count">
      Belum ada waktu dipilih
    </div>

    <button type="button" id="btnModePilihWaktu" class="btn btn-soft" onclick="aktifkanModePilihWaktu()">
      ✅ Pilih Beberapa Waktu
    </button>

    <button type="button" id="btnPilihSemuaWaktu" class="btn btn-warning" onclick="togglePilihSemuaWaktu()" style="display:none;">
      Pilih Semua
    </button>

    <button type="button" id="btnHapusTerpilihWaktu" class="btn btn-danger" onclick="hapusWaktuTerpilih()" style="display:none;">
      Hapus Terpilih
    </button>

    <button type="button" id="btnBatalPilihWaktu" class="btn btn-grey" onclick="batalModePilihWaktu()" style="display:none;">
      Batal
    </button>
  </div>

  <div class="table-wrap">
    <table class="minw">
      <thead>
        <tr>
          <th id="kolomPilihHeaderWaktu" style="width:45px; display:none;">Pilih</th>
          <th style="width:90px;">Jam Mulai</th>
          <th style="width:90px;">Jam Selesai</th>
          <th style="width:100px;">Hari</th>
          <th style="width:120px;">Tanggal</th>
          <th style="width:180px;">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @forelse($waktus as $w)
          <tr
            id="waktu-{{ $w->id }}"
            class="{{
              (
                session('highlight_waktu_id') == $w->id
                ||
                in_array($w->id, session('highlight_waktu_ids', []))
              )
              ? 'highlight-row'
              : ''
            }}"
          >
            <td class="td-center kolom-pilih-waktu" style="display:none;">
              <input
                type="checkbox"
                class="waktu-check"
                value="{{ $w->id }}"
                onclick="updateSelectedCountWaktu()"
              >
            </td>

            <td class="td-center">
              {{ $w->jam_mulai ? \Carbon\Carbon::parse($w->jam_mulai)->format('H:i') : '-' }}
            </td>

            <td class="td-center">
              {{ $w->jam_selesai ? \Carbon\Carbon::parse($w->jam_selesai)->format('H:i') : '-' }}
            </td>

            <td class="td-center">{{ $w->hari ?? '-' }}</td>

            <td class="td-center">
              {{ $w->tanggal_otomatis ?? '-' }}
            </td>

            <td class="td-center">
              <div class="aksi-wrap">
                <a class="btn btn-soft" href="{{ route('admin.waktu.edit', $w->id) }}">
                  Edit
                </a>

                <form method="POST"
                      action="{{ route('admin.waktu.destroy', $w->id) }}"
                      onsubmit="return confirm('Yakin hapus waktu ini?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="td-center" style="color:var(--muted); padding:16px;">
              Data waktu belum ada.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
  <script>

window.addEventListener('load', function () {

    // =========================
    // EDIT RUANGAN
    // =========================

    @if(session('highlight_ruangan_id'))

        const ruanganEdit = document.getElementById(
            'ruangan-{{ session('highlight_ruangan_id') }}'
        );

        if(ruanganEdit){

            ruanganEdit.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        }

    @endif

    // =========================
    // IMPORT RUANGAN
    // =========================

    @if(session('highlight_ruangan_ids'))

        const ruanganIds = @json(session('highlight_ruangan_ids'));

        if(ruanganIds.length > 0){

            const lastRuangan = document.getElementById(
                'ruangan-' + ruanganIds[ruanganIds.length - 1]
            );

            if(lastRuangan){

                lastRuangan.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

            }

        }

    @endif

    // =========================
    // EDIT WAKTU
    // =========================

    @if(session('highlight_waktu_id'))

        const waktuEdit = document.getElementById(
            'waktu-{{ session('highlight_waktu_id') }}'
        );

        if(waktuEdit){

            waktuEdit.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        }

    @endif

    // =========================
    // IMPORT WAKTU
    // =========================

    @if(session('highlight_waktu_ids'))

        const waktuIds = @json(session('highlight_waktu_ids'));

        if(waktuIds.length > 0){

            const lastWaktu = document.getElementById(
                'waktu-' + waktuIds[waktuIds.length - 1]
            );

            if(lastWaktu){

                lastWaktu.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

            }

        }

    @endif

});

let semuaRuanganDipilih = false;

function aktifkanModePilihRuangan() {
    document.body.classList.add('mode-pilih-ruangan');

    document.querySelectorAll('.kolom-pilih-ruangan').forEach(td => {
        td.style.display = 'table-cell';
    });

    document.getElementById('kolomPilihHeaderRuangan').style.display = 'table-cell';
    document.getElementById('selectInfoRuangan').style.display = 'inline';
    document.getElementById('selectedCountRuangan').style.display = 'inline-block';
    document.getElementById('btnPilihSemuaRuangan').style.display = 'inline-block';
    document.getElementById('btnHapusTerpilihRuangan').style.display = 'inline-block';
    document.getElementById('btnBatalPilihRuangan').style.display = 'inline-block';
    document.getElementById('btnModePilihRuangan').style.display = 'none';

    updateSelectedCountRuangan();
}

function batalModePilihRuangan() {
    semuaRuanganDipilih = false;

    document.body.classList.remove('mode-pilih-ruangan');

    document.querySelectorAll('.kolom-pilih-ruangan').forEach(td => {
        td.style.display = 'none';
    });

    document.getElementById('kolomPilihHeaderRuangan').style.display = 'none';

    document.querySelectorAll('.ruangan-check').forEach(cb => {
        cb.checked = false;
    });

    document.getElementById('selectInfoRuangan').style.display = 'none';
    document.getElementById('selectedCountRuangan').style.display = 'none';
    document.getElementById('btnPilihSemuaRuangan').style.display = 'none';
    document.getElementById('btnHapusTerpilihRuangan').style.display = 'none';
    document.getElementById('btnBatalPilihRuangan').style.display = 'none';
    document.getElementById('btnModePilihRuangan').style.display = 'inline-block';
    document.getElementById('btnPilihSemuaRuangan').innerText = 'Pilih Semua';
}

function togglePilihSemuaRuangan() {
    const checks = document.querySelectorAll('.ruangan-check');

    semuaRuanganDipilih = !semuaRuanganDipilih;

    checks.forEach(cb => {
        cb.checked = semuaRuanganDipilih;
    });

    document.getElementById('btnPilihSemuaRuangan').innerText =
        semuaRuanganDipilih ? 'Batalkan Semua' : 'Pilih Semua';

    updateSelectedCountRuangan();
}

function updateSelectedCountRuangan() {
    const total = document.querySelectorAll('.ruangan-check:checked').length;
    const counter = document.getElementById('selectedCountRuangan');

    if (total === 0) {
        counter.innerHTML = '⚠️ Belum ada ruangan dipilih';
    } else {
        counter.innerHTML = '✅ ' + total + ' ruangan dipilih untuk dihapus';
    }
}

function hapusRuanganTerpilih() {
    const ids = Array.from(document.querySelectorAll('.ruangan-check:checked'))
        .map(cb => cb.value);

    if (ids.length === 0) {
        alert('Pilih dulu ruangan yang ingin dihapus.');
        return;
    }

    if (!confirm(`Yakin ingin menghapus ${ids.length} ruangan terpilih?`)) {
        return;
    }

    fetch(`{{ route('admin.ruangan.bulkDelete') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ids: ids
        })
    })
    .then(async res => {
        const data = await res.json();

        if (!res.ok || data.success === false) {
            alert(data.message || 'Gagal menghapus ruangan');
            return;
        }

        location.reload();
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}

let semuaWaktuDipilih = false;

function aktifkanModePilihWaktu() {
    document.body.classList.add('mode-pilih-waktu');

    document.querySelectorAll('.kolom-pilih-waktu').forEach(td => {
        td.style.display = 'table-cell';
    });

    document.getElementById('kolomPilihHeaderWaktu').style.display = 'table-cell';
    document.getElementById('selectInfoWaktu').style.display = 'inline';
    document.getElementById('selectedCountWaktu').style.display = 'inline-block';
    document.getElementById('btnPilihSemuaWaktu').style.display = 'inline-block';
    document.getElementById('btnHapusTerpilihWaktu').style.display = 'inline-block';
    document.getElementById('btnBatalPilihWaktu').style.display = 'inline-block';
    document.getElementById('btnModePilihWaktu').style.display = 'none';

    updateSelectedCountWaktu();
}

function batalModePilihWaktu() {
    semuaWaktuDipilih = false;

    document.body.classList.remove('mode-pilih-waktu');

    document.querySelectorAll('.kolom-pilih-waktu').forEach(td => {
        td.style.display = 'none';
    });

    document.getElementById('kolomPilihHeaderWaktu').style.display = 'none';

    document.querySelectorAll('.waktu-check').forEach(cb => {
        cb.checked = false;
    });

    document.getElementById('selectInfoWaktu').style.display = 'none';
    document.getElementById('selectedCountWaktu').style.display = 'none';
    document.getElementById('btnPilihSemuaWaktu').style.display = 'none';
    document.getElementById('btnHapusTerpilihWaktu').style.display = 'none';
    document.getElementById('btnBatalPilihWaktu').style.display = 'none';
    document.getElementById('btnModePilihWaktu').style.display = 'inline-block';
    document.getElementById('btnPilihSemuaWaktu').innerText = 'Pilih Semua';
}

function togglePilihSemuaWaktu() {
    const checks = document.querySelectorAll('.waktu-check');

    semuaWaktuDipilih = !semuaWaktuDipilih;

    checks.forEach(cb => {
        cb.checked = semuaWaktuDipilih;
    });

    document.getElementById('btnPilihSemuaWaktu').innerText =
        semuaWaktuDipilih ? 'Batalkan Semua' : 'Pilih Semua';

    updateSelectedCountWaktu();
}

function updateSelectedCountWaktu() {
    const total = document.querySelectorAll('.waktu-check:checked').length;
    const counter = document.getElementById('selectedCountWaktu');

    if (total === 0) {
        counter.innerHTML = '⚠️ Belum ada waktu dipilih';
    } else {
        counter.innerHTML = '✅ ' + total + ' waktu dipilih untuk dihapus';
    }
}

function hapusWaktuTerpilih() {
    const ids = Array.from(document.querySelectorAll('.waktu-check:checked'))
        .map(cb => cb.value);

    if (ids.length === 0) {
        alert('Pilih dulu waktu yang ingin dihapus.');
        return;
    }

    if (!confirm(`Yakin ingin menghapus ${ids.length} waktu terpilih?`)) {
        return;
    }

    fetch(`{{ route('admin.waktu.bulkDelete') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ids: ids
        })
    })
    .then(async res => {
        const data = await res.json();

        if (!res.ok || data.success === false) {
            alert(data.message || 'Gagal menghapus waktu');
            return;
        }

        location.reload();
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}

</script>
</body>
</html>
