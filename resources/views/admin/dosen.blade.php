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

    .td-center{ text-align:center; }
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
    <a class="active" href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
  </div>

  <div class="content">
    <div class="card">
      <h3 class="page-title">Kelolah Data Dosen</h3>

      @if(session('ok'))
        <div class="success">{{ session('ok') }}</div>
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

          <form method="POST" action="{{ route('admin.dosen.store') }}">
            @csrf

            <div class="row">
              <div class="col">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password awal dosen" required>
                @error('password') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Kode Dosen</label>
                <input name="kode_dosen" value="{{ old('kode_dosen') }}" placeholder="Contoh: DSN001">
                @error('kode_dosen') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              <div class="col">
                <label>Nama Dosen</label>
                <input name="nama" value="{{ old('nama') }}" placeholder="Contoh: Rifdah Dosen">
                @error('nama') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Program Studi</label>
                <input name="program_studi" value="{{ old('program_studi') }}" placeholder="Contoh: Sistem Informasi">
                @error('program_studi') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              <div class="col">
                <label>NIDN</label>
                <input name="nidn" value="{{ old('nidn') }}" placeholder="Contoh: 9132xxxx">
                @error('nidn') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Email</label>
                <input name="email" value="{{ old('email') }}" placeholder="Contoh: dosen@gmail.com">
                @error('email') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="actions">
              <button class="btn btn-orange" type="submit">Simpan</button>
              <button class="btn btn-soft" type="reset">Reset</button>
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

        <div class="table-wrap">
          <table class="minw">
            <thead>
              <tr>
                <th style="width:90px;">Kode</th>
                <th>Nama Dosen</th>
                <th style="width:180px;">Program Studi</th>
                <th style="width:140px;">NIDN</th>
                <th style="width:220px;">Email</th>
                <th style="width:170px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              @forelse($dosens as $d)
                <tr>
                  <td class="td-center">{{ $d->kode_dosen ?? '-' }}</td>
                  <td>{{ $d->nama ?? '-' }}</td>
                  <td>{{ $d->program_studi ?? '-' }}</td>
                  <td class="td-center">{{ $d->nidn ?? '-' }}</td>
                  <td>{{ $d->email ?? '-' }}</td>

                  <td class="td-center">
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
                  <td colspan="6" class="td-center" style="color:var(--muted); padding:16px;">
                    Data dosen belum ada.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>

    </div>
  </div>
</body>
</html>
