<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Data Dosen</title>

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
      overflow-x: hidden;
    }

    .topbar{
      position: fixed;
      top: 0; left: 0; right: 0;
      height: var(--topbarH);
      z-index: 9999;

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

    .sidebar{
      position: fixed;
      top: var(--topbarH);
      left: 0;
      width: var(--sidebarW);
      height: calc(100vh - var(--topbarH));
      overflow-y: auto;

      background:#fff;
      border-right:1px solid var(--border);
      padding:14px;
      z-index: 9998;
    }

    .sidebar a{
      display:block;
      padding:10px 10px;
      color:var(--orange);
      text-decoration:none;
      border-radius:8px;
      margin-bottom:6px;
      font-size:14px;

      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .sidebar a.active{
      background:var(--orangeSoft);
      font-weight:700;
    }

    .content{
      padding:18px;
      padding-top: calc(var(--topbarH) + 18px);
      margin-left: var(--sidebarW);
      min-height: 100vh;

      min-width: 0;
      overflow-x: hidden;
    }

    .card{
      background:var(--card);
      border-radius:12px;
      padding:16px;
      border:1px solid var(--border);
    }

    .page-title{
      margin:0 0 12px;
      font-size:18px;
    }

    .notice{
      margin:0 0 12px;
      padding:10px 12px;
      border-radius:10px;
      background:var(--orangeSoft);
      color:#7a4b10;
      font-size:13px;
      border:1px solid rgba(229,134,31,.25);
    }

    .grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:14px;
    }

    @media (max-width: 900px){
      :root{ --sidebarW: 270px; }
      .grid{ grid-template-columns: 1fr; }
    }

    .panel{
      border:1px solid var(--border);
      border-radius:12px;
      padding:14px;
      background:#fff;
    }

    .panel h4{
      margin:0 0 10px;
      font-size:14px;
    }

    label{
      display:block;
      font-size:12px;
      color:var(--muted);
      margin-bottom:6px;
    }

    input, select{
      width:100%;
      padding:10px 12px;
      border-radius:10px;
      border:1px solid #ddd;
      outline:none;
      background:#fff;
      font-size:14px;
    }

    input:focus, select:focus{
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
      white-space: nowrap;
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

    .small-err{ color:var(--danger); font-size:12px; margin-top:6px; }
    .success{
      background:#eaffea;
      border:1px solid #c7f0c7;
      color:#1b6b1b;
      padding:10px 12px;
      border-radius:10px;
      font-size:13px;
      margin:0 0 12px;
    }

    .error-alert{
      background:#ffe5e5;
      border:1px solid #ffb3b3;
      color:#b00020;
      padding:12px;
      border-radius:10px;
      font-size:13px;
      margin:0 0 12px;
    }

    .error-alert ul{
      margin:8px 0 0 18px;
      padding:0;
    }

    .table-wrap{
      width: 100%;
      overflow-x: auto;
      overflow-y: hidden;
    }
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
      vertical-align:top;
      font-size:13px;
    }

    th{
      background:#f4f4f4;
      text-align:center;
      font-size:12px;
    }

    .input-password{
      position: relative;
    }

    .input-password input{
      padding-right:40px;
    }

    .eye{
      position:absolute;
      right:12px;
      top:50%;
      transform:translateY(-50%);
      cursor:pointer;
      font-size:16px;
      color:#888;
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

.dosen-check{
    display:none;
    width:18px;
    height:18px;
    cursor:pointer;
}

body.mode-pilih-dosen .dosen-check{
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

.aksi-cell{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;
    flex-wrap:nowrap;
}

.aksi-cell form{
    margin:0;
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
    <a class="active" href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
    <a href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
  </div>

  <div class="content">
    <div class="card">
      <h3 class="page-title">Kelolah Data Dosen</h3>

      @if(session('ok'))
    <div class="success">
        {{ session('ok') }}
    </div>
    @endif

    @if(session('error'))
        <div class="error-alert">

            @if(is_array(session('error')))
                <b>Beberapa data gagal ditambahkan:</b>

                <ul>
                    @foreach(session('error') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @else
                {{ session('error') }}
            @endif

        </div>
    @endif

      {{-- FILTER DOSEN --}}
      <div class="panel" style="margin-bottom:14px;">
        <h4>Filter Data Dosen</h4>

        <form method="GET" action="{{ route('admin.dosen.index') }}">
          <div class="row">
            <div class="col">
              <label>Cari (Kode / Nama / Email / NIDN)</label>
              <input name="q" value="{{ $q ?? '' }}" placeholder="Contoh: DSN001 / Rifdah / email@gmail.com / 9132...">
            </div>
            <div class="col">
              <label>Program Studi</label>
              <input name="prodi" value="{{ $prodi ?? '' }}" placeholder="Contoh: Sistem Informasi">
            </div>
          </div>

          <div class="row" style="margin-top:10px;">
            <div class="col">
              <label>NIDN (opsional)</label>
              <input name="nidn" value="{{ $nidn ?? '' }}" placeholder="Contoh: 9132xxxx">
            </div>
            <div class="col">
              <label>Urutkan</label>
              <select name="urut">
                <option value="kode" {{ ($urut ?? 'kode') === 'kode' ? 'selected' : '' }}>Kode Dosen</option>
                <option value="nama" {{ ($urut ?? 'kode') === 'nama' ? 'selected' : '' }}>Nama Dosen</option>
              </select>
            </div>
          </div>

          <div class="actions">
            <button class="btn btn-orange" type="submit">Terapkan Filter</button>
            <a class="btn btn-soft" href="{{ route('admin.dosen.index') }}">Reset</a>
          </div>
        </form>
      </div>

      <div class="grid">

        {{-- PANEL TAMBAH DOSEN --}}
        <div class="panel">
          <h4>Tambah Dosen</h4>

          <form method="POST"
              action="{{ route('admin.dosen.store') }}"
              autocomplete="off">
@csrf

<!-- ROW 1 -->
<div class="row">

  <!-- NAMA -->
  <div class="col">
    <label>Nama Dosen</label>

    <input
      name="nama"
      value="{{ old('nama') }}"
      placeholder="Contoh: Rifdah Dosen"
    >

    @error('nama')
      <div class="small-err">{{ $message }}</div>
    @enderror
  </div>

  <!-- NIDN -->
  <div class="col">
  <label>NIDN (harus 10 angka)</label>

  <input
    type="text"
    name="nidn"
    value="{{ old('nidn') }}"
    placeholder="Contoh: Karakter harus"
    maxlength="10"
    minlength="10"
    pattern="[0-9]{10}"
    inputmode="numeric"
  >

  @error('nidn')
    <div class="small-err">{{ $message }}</div>
  @enderror
</div>

</div>

<!-- ROW 2 -->
<div class="row" style="margin-top:10px;">

  <!-- PROGRAM STUDI -->
  <div class="col">

    <label>Program Studi</label>

    <select name="program_studi" id="program_studi">

      <option value="">-- Pilih Program Studi --</option>

      <option>Ilmu Komputer</option>
      <option>Sistem Informasi</option>
      <option>Matematika</option>
      <option>Teknik Sipil</option>
      <option>Sains Data</option>
      <option>Teknologi Pangan</option>
      <option>Bioteknologi</option>
      <option>Teknik Arsitektur</option>
      <option>Bisnis Digital</option>
      <option>Sains Aktuaria</option>
      <option>Teknik Mesin</option>
      <option>Teknik Perkapalan</option>
      <option>Teknik Elektro</option>
      <option>Teknik Industri</option>
      <option>Teknik Lingkungan</option>
      <option>Teknik Sistem Energi</option>
      <option>Teknik Metalurgi</option>
      <option>Teknik Robotika & Kecerdasan Buatan</option>

    </select>

    @error('program_studi')
      <div class="small-err">{{ $message }}</div>
    @enderror

  </div>

  <!-- MATA KULIAH -->
  <div class="col">

    <label>Mata Kuliah yang Diampu</label>

    <select name="mata_kuliah_id" id="mata_kuliah_id">

      <option value="">
        -- Pilih Mata Kuliah --
      </option>

      @foreach($matakuliahs as $mk)

      <option
        value="{{ $mk->id }}"
        data-prodi="{{ $mk->program_studi }}"
      >
        {{ $mk->nama_mk }}
      </option>

    @endforeach

    </select>

  </div>

</div>

<!-- ROW 3 -->
<div class="row" style="margin-top:10px;">

  <!-- TAHUN AJARAN -->
  <div class="col">

    <label>Tahun Ajaran</label>

    <select name="tahun_ajaran">

      <option value="2025/2026">
        2025/2026
      </option>

      <option value="2026/2027" selected>
        2026/2027
      </option>

      <option value="2027/2028">
        2027/2028
      </option>

    </select>

  </div>

  <!-- KODE DOSEN -->
  <div class="col">

    <label>Kode Dosen</label>

    <input
      name="kode_dosen"
      value="{{ old('kode_dosen') }}"
      placeholder="Contoh: DSN001"
    >

    @error('kode_dosen')
      <div class="small-err">{{ $message }}</div>
    @enderror

  </div>

</div>

<!-- ROW 4 -->
<div class="row" style="margin-top:10px;">

  <!-- EMAIL -->
  <div class="col">

    <label>Email</label>

    <input
      type="email"
      name="email"
      value=""
      placeholder="Contoh: dosen@gmail.com"
      autocomplete="off"
    >

    @error('email')
      <div class="small-err">{{ $message }}</div>
    @enderror

  </div>

  <!-- PASSWORD -->
  <div class="col">

    <label>Password</label>

    <div class="input-password">

      <input
        type="password"
        id="password"
        name="password"
        placeholder="Masukkan password awal dosen"
        autocomplete="new-password"
        required
      >

      <span onclick="togglePassword()" class="eye">
        👁️
      </span>

    </div>

    @error('password')
      <div class="small-err">{{ $message }}</div>
    @enderror

  </div>

</div>

<div class="actions">
  <button class="btn btn-orange" type="submit">
    Simpan
  </button>

  <button class="btn btn-soft" type="reset">
    Reset
  </button>
</div>

</form>

      </div>

        {{-- PANEL IMPORT DOSEN --}}
        <div class="panel">
          <h4>Tambah / Import Dosen</h4>

          <div class="notice">
            Masukkan file Excel untuk import dosen. (xlsx/xls/csv)
          </div>

          <form method="POST" action="{{ route('admin.dosen.import') }}" enctype="multipart/form-data">
            @csrf

            {{-- supaya setelah import, user tidak “kehilangan konteks” filter (kalau kamu pakai redirect back suatu saat) --}}
            <input type="hidden" name="q" value="{{ $q ?? '' }}">
            <input type="hidden" name="prodi" value="{{ $prodi ?? '' }}">
            <input type="hidden" name="nidn" value="{{ $nidn ?? '' }}">
            <input type="hidden" name="urut" value="{{ $urut ?? 'kode' }}">

            <label>Masukkan File Excel</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv">
            @error('file') <div class="small-err">{{ $message }}</div> @enderror

            <div class="actions">
              <button class="btn btn-orange" type="submit">Import</button>
            </div>
          </form>

          <div style="margin-top:10px; font-size:12px; color:var(--muted);">
            <b>Format kolom Excel (heading):</b><br>
            kode_dosen, nama, program_studi, nidn, email, password
          </div>
        </div>
      </div>

     {{-- DAFTAR DOSEN --}}
<div class="panel" style="margin-top:14px;">
  <h4>Daftar Dosen</h4>

  <div class="bulk-bar">
    <div id="selectInfoDosen" class="select-mode-info" style="display:none;">
      Pilih beberapa dosen yang ingin dihapus
    </div>

    <div id="selectedCountDosen" class="selected-count">
      Belum ada dosen dipilih
    </div>

    <button type="button" id="btnModePilihDosen" class="btn btn-soft" onclick="aktifkanModePilihDosen()">
      ✅ Pilih Beberapa Dosen
    </button>

    <button type="button" id="btnPilihSemuaDosen" class="btn btn-warning" onclick="togglePilihSemuaDosen()" style="display:none;">
      Pilih Semua
    </button>

    <button type="button" id="btnHapusTerpilihDosen" class="btn btn-danger" onclick="hapusDosenTerpilih()" style="display:none;">
      Hapus Terpilih
    </button>

    <button type="button" id="btnBatalPilihDosen" class="btn btn-grey" onclick="batalModePilihDosen()" style="display:none;">
      Batal
    </button>
  </div>

  <div class="table-wrap">
          <table class="minw">
            <thead>
              <tr>
                <th id="kolomPilihHeader"
                    style="width:45px;display:none;">
                    Pilih
                </th>
                <th style="width:90px;">Kode</th>
                <th>Nama Dosen</th>
                <th style="width:180px;">Program Studi</th>
                <th style="width:140px;">NIDN</th>
                <th style="width:220px;">Email</th>
                <th style="width:230px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              @forelse($dosens as $d)

              <tr
                  id="dosen-{{ $d->id }}"
                  class="{{ session('highlight_id') == $d->id ? 'highlight-row' : '' }}"
              >
                  <td
                      class="td-center kolom-pilih"
                      style="display:none;"
                  >
                      <input
                          type="checkbox"
                          class="dosen-check"
                      value="{{ $d->id }}"
                      onclick="updateSelectedCountDosen()"
                    >
                  </td>

                  <td class="td-center">{{ $d->kode_dosen ?? '-' }}</td>
                  <td>{{ $d->nama ?? '-' }}</td>
                  <td>{{ $d->program_studi ?? '-' }}</td>
                  <td class="td-center">{{ $d->nidn ?? '-' }}</td>
                  <td>{{ $d->email ?? '-' }}</td>

                  <td class="td-center aksi-cell">
                    <a href="{{ route('admin.dosen.edit', $d->id) }}" class="btn btn-soft">Edit</a>

                    <form action="{{ route('admin.dosen.destroy', $d->id) }}"
                          method="POST"
                          style="display:inline-block;"
                          onsubmit="return confirm('Yakin mau menghapus dosen ini?\n\n{{ $d->nama }} ({{ $d->kode_dosen }})');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger" type="submit">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="td-center" style="color:var(--muted); padding:16px;">
                    Data dosen belum ada.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

    </div>
  </div>

 <script>
function togglePassword() {
    const input = document.getElementById("password");

    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

@if(session('highlight_id'))

window.addEventListener('load', function () {

    const row = document.getElementById(
        'dosen-{{ session('highlight_id') }}'
    );

    if(row){

        row.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

    }

});

@endif

const prodiSelect = document.getElementById('program_studi');

const mkSelect = document.getElementById('mata_kuliah_id');

const semuaOptionMK = Array.from(
    mkSelect.querySelectorAll('option')
);

prodiSelect.addEventListener('change', function () {

    const prodiDipilih = this.value;

    mkSelect.innerHTML = '';

    // option default
    const defaultOption = document.createElement('option');

    defaultOption.value = '';
    defaultOption.textContent = '-- Pilih Mata Kuliah --';

    mkSelect.appendChild(defaultOption);

    semuaOptionMK.forEach(function(option){

        const prodiMK = option.getAttribute('data-prodi');

        if(
            prodiMK === prodiDipilih
        ){
            mkSelect.appendChild(option);
        }

    });

});

let modePilihDosenAktif = false;
let semuaDosenDipilih = false;

function aktifkanModePilihDosen() {
    modePilihDosenAktif = true;
    document.body.classList.add('mode-pilih');

    document.querySelectorAll('.kolom-pilih').forEach(td=>td.style.display='table-cell');
    document.getElementById('kolomPilihHeader').style.display='table-cell';
    document.getElementById('selectInfoDosen').style.display = 'inline';
    document.getElementById('selectedCountDosen').style.display = 'inline-block';
    document.getElementById('btnPilihSemuaDosen').style.display = 'inline-block';
    document.getElementById('btnHapusTerpilihDosen').style.display = 'inline-block';
    document.getElementById('btnBatalPilihDosen').style.display = 'inline-block';
    document.getElementById('btnModePilihDosen').style.display = 'none';

    updateSelectedCountDosen();
}

function batalModePilihDosen() {
    modePilihDosenAktif = false;
    semuaDosenDipilih = false;

    document.body.classList.remove('mode-pilih-dosen');
    document.querySelectorAll('.kolom-pilih').forEach(td=>td.style.display='none');
    document.getElementById('kolomPilihHeader').style.display='none';
    document.querySelectorAll('.dosen-check').forEach(cb => {cb.checked = false; });
    document.getElementById('selectInfoDosen').style.display = 'none';
    document.getElementById('selectedCountDosen').style.display = 'none';
    document.getElementById('btnPilihSemuaDosen').style.display = 'none';
    document.getElementById('btnHapusTerpilihDosen').style.display = 'none';
    document.getElementById('btnBatalPilihDosen').style.display = 'none';
    document.getElementById('btnModePilihDosen').style.display = 'inline-block';

    document.getElementById('btnPilihSemuaDosen').innerText = 'Pilih Semua';
}

function togglePilihSemuaDosen() {
    const checks = document.querySelectorAll('.dosen-check');

    semuaDosenDipilih = !semuaDosenDipilih;

    checks.forEach(cb => {
        cb.checked = semuaDosenDipilih;
    });

    document.getElementById('btnPilihSemuaDosen').innerText =
        semuaDosenDipilih ? 'Batalkan Semua' : 'Pilih Semua';

    updateSelectedCountDosen();
}

function updateSelectedCountDosen() {
    const total = document.querySelectorAll('.dosen-check:checked').length;
    const counter = document.getElementById('selectedCountDosen');

    if (total === 0) {
        counter.innerHTML = '⚠️ Belum ada dosen dipilih';
    } else {
        counter.innerHTML = '✅ ' + total + ' dosen dipilih untuk dihapus';
    }
}

function hapusDosenTerpilih() {
    const ids = Array.from(document.querySelectorAll('.dosen-check:checked'))
        .map(cb => cb.value);

    if (ids.length === 0) {
        alert('Pilih dulu dosen yang ingin dihapus.');
        return;
    }

    if (!confirm(`Yakin ingin menghapus ${ids.length} dosen terpilih?`)) {
        return;
    }

    fetch(`{{ route('admin.dosen.bulkDelete') }}`, {
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
            alert(data.message || 'Gagal menghapus dosen');
            return;
        }

        localStorage.setItem('success_dosen', data.message);
        location.reload();
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const msg = localStorage.getItem('success_dosen');

    if (msg) {
        alert(msg);
        localStorage.removeItem('success_dosen');
    }
});
</script>
</body>
</html>
