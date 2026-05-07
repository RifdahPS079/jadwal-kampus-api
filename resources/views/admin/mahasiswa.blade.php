<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Data Mahasiswa</title>

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
      --sidebarW: 220px;
    }

    *{ box-sizing:border-box; }

    html, body{
      margin:0;
      font-family: Arial, sans-serif;
      background:var(--bg);
      color:var(--text);
      overflow-x: hidden;
    }

    /* TOPBAR FIXED */
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

    /* SIDEBAR FIXED */
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

    /* CONTENT */
    .content{
      padding: 18px;
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

    .grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:14px;
    }

    @media (max-width: 900px){
      :root{ --sidebarW: 190px; }
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

    .notice{
      margin:0 0 12px;
      padding:10px 12px;
      border-radius:10px;
      background:var(--orangeSoft);
      color:#7a4b10;
      font-size:13px;
      border:1px solid rgba(229,134,31,.25);
    }

    label{
      display:block;
      font-size:12px;
      color:var(--muted);
      margin-bottom:6px;
    }
  input,
  select{
    width:100%;
    padding:10px 12px;
    border-radius:10px;
    border:1px solid #ddd;
    outline:none;
    background:#fff;
    font-size:14px;
  }

  input:focus,
  select:focus{
    border-color:var(--orange);
    box-shadow:0 0 0 4px rgba(229,134,31,.12);
  }

    .row{ display:flex; gap:10px; flex-wrap:wrap; }
    .col{ flex:1; min-width:250px; }

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

    .error{
      background:var(--dangerSoft);
      border:1px solid rgba(176,0,32,.25);
      color:var(--danger);
      padding:10px 12px;
      border-radius:10px;
      font-size:13px;
      margin:0 0 12px;
    }

    .table-wrap{
      width: 100%;
      overflow-x: auto;
      overflow-y: hidden;
    }
    .minw{ min-width: 1100px; }

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

    .password-wrapper{
  position: relative;
  width: 100%;
}

.password-wrapper input{
  padding-right: 40px; /* kasih ruang buat icon */
}

.toggle-password{
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 16px;
  color: #777;
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
  </style>
</head>

<body>
  <div class="topbar">
    <div><b>Web Admin Penjadwalan Perkuliahan</b></div>

    <div class="topbar-right">
      <span>Admin</span>
      <!-- <div class="badge">A</div> -->

      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout-btn" type="submit">Logout</button>
      </form>
    </div>
  </div>

  <div class="sidebar">
    <a href="{{ route('admin.monitoring') }}">Monitoring Jadwal</a>
    <a href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a class="active" href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
  </div>

  <div class="content">
    <div class="card">
      <h3 class="page-title">Kelolah Data Mahasiswa</h3>

      @if(session('ok'))
        <div class="success">{{ session('ok') }}</div>
      @endif

      @if(session('error'))

      <div class="error">

          @if(is_array(session('error')))

              <b>Beberapa data gagal ditambahkan:</b>

              <ul style="margin:8px 0 0; padding-left:18px;">

                  @foreach(session('error') as $err)

                      <li>{{ $err }}</li>

                  @endforeach

              </ul>

          @else

              {{ session('error') }}

          @endif

      </div>

      @endif

      {{-- FILTER (konsep sama seperti matakuliah) --}}
      <div class="panel" style="margin-bottom:14px;">
        <h4>Filter Data Mahasiswa</h4>

        <form method="GET" action="{{ route('admin.mahasiswa.index') }}">
          <div class="row">
            <div class="col">
              <label>Cari (Nama / NIM / Email)</label>
              <input name="q" value="{{ $q ?? '' }}" placeholder="Contoh: Adhi / 221011079 / email@gmail.com">
            </div>
            <div class="col">
              <label>Program Studi</label>
             <select name="prodi">

    <option value="">
        -- Semua Program Studi --
    </option>

    <option value="Ilmu Komputer"
        {{ ($prodi ?? '') == 'Ilmu Komputer' ? 'selected' : '' }}>
        Ilmu Komputer
    </option>

    <option value="Sistem Informasi"
        {{ ($prodi ?? '') == 'Sistem Informasi' ? 'selected' : '' }}>
        Sistem Informasi
    </option>

    <option value="Matematika"
        {{ ($prodi ?? '') == 'Matematika' ? 'selected' : '' }}>
        Matematika
    </option>

    <option value="Teknik Sipil"
        {{ ($prodi ?? '') == 'Teknik Sipil' ? 'selected' : '' }}>
        Teknik Sipil
    </option>

    <option value="Sains Data"
        {{ ($prodi ?? '') == 'Sains Data' ? 'selected' : '' }}>
        Sains Data
    </option>

    <option value="Teknologi Pangan"
        {{ ($prodi ?? '') == 'Teknologi Pangan' ? 'selected' : '' }}>
        Teknologi Pangan
    </option>

    <option value="Bioteknologi"
        {{ ($prodi ?? '') == 'Bioteknologi' ? 'selected' : '' }}>
        Bioteknologi
    </option>

    <option value="Teknik Arsitektur"
        {{ ($prodi ?? '') == 'Teknik Arsitektur' ? 'selected' : '' }}>
        Teknik Arsitektur
    </option>

    <option value="Bisnis Digital"
        {{ ($prodi ?? '') == 'Bisnis Digital' ? 'selected' : '' }}>
        Bisnis Digital
    </option>

    <option value="Sains Aktuaria"
        {{ ($prodi ?? '') == 'Sains Aktuaria' ? 'selected' : '' }}>
        Sains Aktuaria
    </option>

    <option value="Teknik Mesin"
        {{ ($prodi ?? '') == 'Teknik Mesin' ? 'selected' : '' }}>
        Teknik Mesin
    </option>

    <option value="Teknik Perkapalan"
        {{ ($prodi ?? '') == 'Teknik Perkapalan' ? 'selected' : '' }}>
        Teknik Perkapalan
    </option>

    <option value="Teknik Elektro"
        {{ ($prodi ?? '') == 'Teknik Elektro' ? 'selected' : '' }}>
        Teknik Elektro
    </option>

    <option value="Teknik Industri"
        {{ ($prodi ?? '') == 'Teknik Industri' ? 'selected' : '' }}>
        Teknik Industri
    </option>

    <option value="Teknik Lingkungan"
        {{ ($prodi ?? '') == 'Teknik Lingkungan' ? 'selected' : '' }}>
        Teknik Lingkungan
    </option>

    <option value="Teknik Sistem Energi"
        {{ ($prodi ?? '') == 'Teknik Sistem Energi' ? 'selected' : '' }}>
        Teknik Sistem Energi
    </option>

    <option value="Teknik Metalurgi"
        {{ ($prodi ?? '') == 'Teknik Metalurgi' ? 'selected' : '' }}>
        Teknik Metalurgi
    </option>

    <option value="Teknik Robotika & Kecerdasan Buatan"
        {{ ($prodi ?? '') == 'Teknik Robotika & Kecerdasan Buatan' ? 'selected' : '' }}>
        Teknik Robotika & Kecerdasan Buatan
    </option>

</select>
            </div>
          </div>

          <div class="row" style="margin-top:10px;">
            <div class="col">
              <label>Kelas</label>
              <input name="kelas" value="{{ $kelas ?? '' }}" placeholder="Contoh: IK22A">
            </div>
            <div class="col">
              <label>Angkatan</label>
              <input name="angkatan" value="{{ $angk ?? '' }}" placeholder="Contoh: 2022">
            </div>
          </div>

          <div class="actions">
            <button class="btn btn-orange" type="submit">Terapkan Filter</button>
            <a class="btn btn-soft" href="{{ route('admin.mahasiswa.index') }}">Reset</a>
          </div>
        </form>
      </div>

      <div class="grid">

        {{-- PANEL TAMBAH MAHASISWA --}}
        <div class="panel">
          <h4>Tambah Mahasiswa</h4>

          <form method="POST" action="{{ route('admin.mahasiswa.store') }}" autocomplete="off">
            @csrf

            <div class="row">

              <div class="col">
                  <label>Nama Mahasiswa</label>
                  <input name="nama" value="{{ old('nama') }}" placeholder="Contoh: Muhammad Adhi">
                  @error('nama') <div class="small-err">{{ $message }}</div> @enderror
                </div>
              <div class="col">
                <label>NIM</label>
                <input
                    type="text"
                    name="nim"
                    value="{{ old('nim') }}"
                    placeholder="Contoh: 221011079"
                    maxlength="9"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                >
                @error('nim') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Program Studi</label>
                <select name="program_studi">

    <option value="">
        -- Pilih Program Studi --
    </option>

    <option value="Ilmu Komputer">Ilmu Komputer</option>

    <option value="Sistem Informasi">Sistem Informasi</option>

    <option value="Matematika">Matematika</option>

    <option value="Teknik Sipil">Teknik Sipil</option>

    <option value="Sains Data">Sains Data</option>

    <option value="Teknologi Pangan">Teknologi Pangan</option>

    <option value="Bioteknologi">Bioteknologi</option>

    <option value="Teknik Arsitektur">Teknik Arsitektur</option>

    <option value="Bisnis Digital">Bisnis Digital</option>

    <option value="Sains Aktuaria">Sains Aktuaria</option>

    <option value="Teknik Mesin">Teknik Mesin</option>

    <option value="Teknik Perkapalan">Teknik Perkapalan</option>

    <option value="Teknik Elektro">Teknik Elektro</option>

    <option value="Teknik Industri">Teknik Industri</option>

    <option value="Teknik Lingkungan">Teknik Lingkungan</option>

    <option value="Teknik Sistem Energi">Teknik Sistem Energi</option>

    <option value="Teknik Metalurgi">Teknik Metalurgi</option>

    <option value="Teknik Robotika & Kecerdasan Buatan">
        Teknik Robotika & Kecerdasan Buatan
    </option>

</select>
                @error('program_studi') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              <div class="col">
                <label>Kelas</label>
                <input name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: IK22A">
                @error('kelas') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              
              <div class="col">

              <label>Angkatan</label>

              <input
                  type="text"
                  name="angkatan"
                  value="{{ old('angkatan') }}"
                  placeholder="Contoh: 2022"
                  maxlength="4"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')"
              >

              @error('angkatan')
                  <div class="small-err">{{ $message }}</div>
              @enderror

          </div>
            </div>

           <div class="row" style="margin-top:10px;">

            <!-- EMAIL -->
            <div class="col">
              <label>Email</label>
              <input
                  type="email"
                  name="email"
                  value=""
                  placeholder="Contoh: mahasiswa@gmail.com"
                  autocomplete="off"
              >
              @error('email') <div class="small-err">{{ $message }}</div> @enderror
            </div>

            <!-- PASSWORD -->
            <div class="col">
              <label>Password</label>

              <div class="input-password">
                <div class="password-wrapper">
                  <input
                      type="password"
                      id="password"
                      name="password"
                      placeholder="Masukkan password"
                      autocomplete="new-password"
                  >

                  <span class="toggle-password" onclick="togglePassword()">👁️</span>
                </div>
              </div>

              @error('password') <div class="small-err">{{ $message }}</div> @enderror
            </div>

          </div>

            <div class="actions">
              <button class="btn btn-orange" type="submit">Simpan</button>
              <button class="btn btn-soft" type="reset">Reset</button>
            </div>
          </form>
        </div>

        {{-- PANEL IMPORT MAHASISWA --}}
        <div class="panel">
          <h4>Tambah / Import Mahasiswa</h4>

          <div class="notice">
            Masukkan file Excel untuk import mahasiswa. (xlsx/xls/csv)
          </div>

          <form method="POST" action="{{ route('admin.mahasiswa.import') }}" enctype="multipart/form-data">
            @csrf

            {{-- biar setelah import tetap kembali ke filter yang sedang dipakai --}}
            <input type="hidden" name="q" value="{{ $q ?? '' }}">
            <input type="hidden" name="prodi" value="{{ $prodi ?? '' }}">
            <input type="hidden" name="kelas" value="{{ $kelas ?? '' }}">
            <input type="hidden" name="angkatan" value="{{ $angk ?? '' }}">

            <label>Masukkan File Excel</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv">
            @error('file') <div class="small-err">{{ $message }}</div> @enderror

            <div class="actions">
              <button class="btn btn-orange" type="submit">Import</button>
            </div>
          </form>

          <div style="margin-top:10px; font-size:12px; color:var(--muted);">
            <b>Format kolom Excel (heading):</b><br>
            nim, nama, program_studi, kelas, angkatan, email, password
          </div>
        </div>

      </div>

      {{-- DAFTAR MAHASISWA --}}
      <div class="panel" style="margin-top:14px;">
        <h4>Daftar Mahasiswa</h4>

        <div class="table-wrap">
          <table class="minw">
            <thead>
              <tr>
                <th style="width:200px;">Nama Mahasiswa</th>
                <th style="width:150px;">Program Studi</th>
                <th style="width:100px;">NIM</th>
                <th style="width:80px;">Kelas</th>
                <th style="width:100px;">Angkatan</th>
                <th style="width:180px;">Email</th>
                <th style="width:170px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              @forelse($mahasiswas as $m)
                <tr
                    id="mahasiswa-{{ $m->id }}"
                    class="{{ session('highlight_id') == $m->id ? 'highlight-row' : '' }}"
                >
                  <td>{{ $m->nama ?? '-' }}</td>
                  <td>{{ $m->program_studi ?? '-' }}</td>
                  <td class="td-center">{{ $m->nim ?? '-' }}</td>
                  <td class="td-center">{{ $m->kelas ?? '-' }}</td>
                  <td class="td-center">{{ $m->angkatan ?? '-' }}</td>
                  <td>{{ $m->email ?? '-' }}</td>

                  <td class="td-center">
                    <a href="{{ route('admin.mahasiswa.edit', $m->id) }}" class="btn btn-soft">Edit</a>

                    <form action="{{ route('admin.mahasiswa.destroy', $m->id) }}"
                          method="POST"
                          style="display:inline-block;"
                          onsubmit="return confirm('Yakin mau menghapus mahasiswa ini?\n\n{{ $m->nama }} ({{ $m->nim }})');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger" type="submit">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="td-center" style="color:var(--muted); padding:16px;">
                    Data mahasiswa belum ada.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

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
        'mahasiswa-{{ session('highlight_id') }}'
    );

    if(row){

        row.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

    }

});

@endif
</script>
</body>
</html>
