<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Mata Kuliah</title>

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
      top:0; left:0; right:0;
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
      left:0;
      width: var(--sidebarW);
      height: calc(100vh - var(--topbarH));
      overflow-y:auto;

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
      overflow:hidden;
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

      min-width:0;
      overflow-x:hidden;
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

    .actions{ display:flex; gap:10px; margin-top:12px; flex-wrap:wrap; align-items:center; }

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

    .small-err{ color:var(--danger); font-size:12px; margin-top:6px; }

    .table-wrap{
      width:100%;
      overflow-x:auto;
      overflow-y:hidden;
    }
    .minw{ min-width: 1200px; }

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
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a class="active" href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
  </div>

  <div class="content">
    <div class="card">
      <h3 class="page-title">Kelolah Mata Kuliah</h3>

      @if(session('ok'))
        <div class="success">{{ session('ok') }}</div>
      @endif

      @if(session('error'))
      <div class="error-alert">
          @if(is_array(session('error')))
              <ul style="margin:0; padding-left:18px;">
                  @foreach(session('error') as $err)
                      <li>{{ $err }}</li>
                  @endforeach
              </ul>
          @else
              {{ session('error') }}
          @endif

      </div>

      @endif

      {{-- FILTER --}}
      <div class="panel" style="margin-bottom:14px;">
        <h4>Filter Data</h4>

        <form method="GET" action="{{ route('admin.matakuliah.index') }}">
          <div class="row">
            <div class="col">
              <label>Tahun Ajaran</label>
              <input name="tahun_ajaran" value="{{ $tahunAjaran ?? '' }}" placeholder="Contoh: 2025/2026">
            </div>
            <div class="col">
              <label>Semester</label>
              <input type="number" min="1" max="14" name="semester" value="{{ $semester ?? 1 }}">
            </div>
          </div>

          <div class="row" style="margin-top:10px;">
            <div class="col">
              <label>Program Studi</label>
              <select name="prodi">
              <option value="">-- Semua Prodi --</option>
              @foreach($prodis as $p)
                <option value="{{ $p }}" {{ ($qProdi ?? '') == $p ? 'selected' : '' }}>
                  {{ $p }}
                </option>
              @endforeach
            </select>
            </div>
            <div class="col">
              <label>Cari Dosen (Kode / Nama)</label>
              <input name="dosen" value="{{ $qDosen ?? '' }}" placeholder="Contoh: DSN001 atau Rifdah">
            </div>
          </div>

          <div class="actions">
            <button class="btn btn-orange" type="submit">Terapkan Filter</button>
            <a class="btn btn-soft" href="{{ route('admin.matakuliah.index') }}">Reset</a>
          </div>
        </form>
      </div>

      <div class="grid">
        {{-- TAMBAH MANUAL --}}
        <div class="panel">
          <h4>Tambah Mata Kuliah</h4>

          <form method="POST" action="{{ route('admin.matakuliah.store') }}">
            @csrf

            <div class="row">
              <div class="col">
                <label>Kode Mata Kuliah</label>
                <input name="kode_mk" value="{{ old('kode_mk') }}" placeholder="Contoh: IL101">
                @error('kode_mk') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              <div class="col">
                <label>Nama Mata Kuliah</label>
                <input name="nama_mk" value="{{ old('nama_mk') }}" placeholder="Contoh: Pengantar Pemrograman">
                @error('nama_mk') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Program Studi</label>
                    <select name="program_studi">

      <option value="">-- Pilih Program Studi --</option>

      <option value="Ilmu Komputer"
        {{ old('program_studi') == 'Ilmu Komputer' ? 'selected' : '' }}>
        Ilmu Komputer
      </option>

      <option value="Sistem Informasi"
        {{ old('program_studi') == 'Sistem Informasi' ? 'selected' : '' }}>
        Sistem Informasi
      </option>

      <option value="Matematika"
        {{ old('program_studi') == 'Matematika' ? 'selected' : '' }}>
        Matematika
      </option>

      <option value="Teknik Sipil"
        {{ old('program_studi') == 'Teknik Sipil' ? 'selected' : '' }}>
        Teknik Sipil
      </option>

      <option value="Sains Data"
        {{ old('program_studi') == 'Sains Data' ? 'selected' : '' }}>
        Sains Data
      </option>

      <option value="Teknologi Pangan"
        {{ old('program_studi') == 'Teknologi Pangan' ? 'selected' : '' }}>
        Teknologi Pangan
      </option>

      <option value="Bioteknologi"
        {{ old('program_studi') == 'Bioteknologi' ? 'selected' : '' }}>
        Bioteknologi
      </option>

      <option value="Teknik Arsitektur"
        {{ old('program_studi') == 'Teknik Arsitektur' ? 'selected' : '' }}>
        Teknik Arsitektur
      </option>

      <option value="Bisnis Digital"
        {{ old('program_studi') == 'Bisnis Digital' ? 'selected' : '' }}>
        Bisnis Digital
      </option>

      <option value="Sains Aktuaria"
        {{ old('program_studi') == 'Sains Aktuaria' ? 'selected' : '' }}>
        Sains Aktuaria
      </option>

      <option value="Teknik Mesin"
        {{ old('program_studi') == 'Teknik Mesin' ? 'selected' : '' }}>
        Teknik Mesin
      </option>

      <option value="Teknik Perkapalan"
        {{ old('program_studi') == 'Teknik Perkapalan' ? 'selected' : '' }}>
        Teknik Perkapalan
      </option>

      <option value="Teknik Elektro"
        {{ old('program_studi') == 'Teknik Elektro' ? 'selected' : '' }}>
        Teknik Elektro
      </option>

      <option value="Teknik Industri"
        {{ old('program_studi') == 'Teknik Industri' ? 'selected' : '' }}>
        Teknik Industri
      </option>

      <option value="Teknik Lingkungan"
        {{ old('program_studi') == 'Teknik Lingkungan' ? 'selected' : '' }}>
        Teknik Lingkungan
      </option>

      <option value="Teknik Sistem Energi"
        {{ old('program_studi') == 'Teknik Sistem Energi' ? 'selected' : '' }}>
        Teknik Sistem Energi
      </option>

      <option value="Teknik Metalurgi"
        {{ old('program_studi') == 'Teknik Metalurgi' ? 'selected' : '' }}>
        Teknik Metalurgi
      </option>

      <option value="Teknik Robotika & Kecerdasan Buatan"
        {{ old('program_studi') == 'Teknik Robotika & Kecerdasan Buatan' ? 'selected' : '' }}>
        Teknik Robotika & Kecerdasan Buatan
      </option>

    </select>
                @error('program_studi') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              <div class="col">
                <label>SKS</label>
                <select name="sks">

  <option value="">-- Pilih SKS --</option>

  @for($i = 1; $i <= 5; $i++)

    <option value="{{ $i }}"
      {{ old('sks') == $i ? 'selected' : '' }}>
      {{ $i }} SKS
    </option>

  @endfor

</select>
                @error('sks') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Dosen Pengampu (periode ini)</label>
                <select name="dosen_id" required>
                  <option value="">-- Pilih Dosen --</option>
                  @foreach($dosens as $d)
                    <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                      {{ $d->kode_dosen }} - {{ $d->nama }}
                    </option>
                  @endforeach
                </select>
                @error('dosen_id') <div class="small-err">{{ $message }}</div> @enderror
              </div>

              <div class="col">
                <label>Semester (periode ini)</label>
                <input type="number" min="1" max="14" name="semester" value="{{ old('semester', $semester ?? 1) }}">
                @error('semester') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="row" style="margin-top:10px;">
              <div class="col">
                <label>Tahun Ajaran (periode ini)</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran ?? '') }}" placeholder="Contoh: 2025/2026">
                @error('tahun_ajaran') <div class="small-err">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="actions">
              <button class="btn btn-orange" type="submit">Simpan</button>
              <button class="btn btn-soft" type="reset">Reset</button>
            </div>
          </form>
        </div>

        {{-- IMPORT --}}
        <div class="panel">
          <h4>Tambah / Import Mata Kuliah</h4>

          <div class="notice">
            Masukkan file Excel untuk import mata kuliah. (xlsx/xls/csv)
          </div>

          <form method="POST" action="{{ route('admin.matakuliah.import') }}" enctype="multipart/form-data">
            @csrf

            {{-- supaya setelah import tetap kembali ke filter yg sama --}}
            <input type="hidden" name="semester" value="{{ $semester ?? 1 }}">
            <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran ?? '' }}">
            <input type="hidden" name="prodi" value="{{ $qProdi ?? '' }}">
            <input type="hidden" name="dosen" value="{{ $qDosen ?? '' }}">

            <label>Masukkan File Excel</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
            @error('file') <div class="small-err">{{ $message }}</div> @enderror

            <div class="actions">
              <button class="btn btn-orange" type="submit">Import</button>
            </div>
          </form>

          <div style="margin-top:10px; color:var(--muted); font-size:12px;">
            <b>Format kolom Excel (heading):</b><br>
            kode_mk, nama_mk, program_studi, sks, semester, tahun_ajaran, kode_dosen
          </div>
        </div>
      </div>

      {{-- DAFTAR --}}
      <div class="panel" style="margin-top:14px;">
        <h4>Daftar Mata Kuliah</h4>

        <div class="table-wrap">
          <table class="minw">
            <thead>
              <tr>
                <th style="width:120px;">Kode</th>
                <th>Nama Mata Kuliah</th>
                <th style="width:180px;">Program Studi</th>
                <th style="width:90px;">SKS</th>
                <th style="width:90px;">Semester</th>
                <th style="width:260px;">Dosen Pengampu ({{ $tahunAjaran ?? '-' }})</th>
                <th style="width:200px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              @forelse($mataKuliahs as $mk)
                @php
                 $pengampu = $mk->pengampus
                  ->where('semester', $semester)
                  ->where('tahun_ajaran', $tahunAjaran)
                  ->first();
                @endphp

                  <tr
                      id="matakuliah-{{ $mk->id }}"
                      class="{{ session('highlight_id') == $mk->id ? 'highlight-row' : '' }}"
                  >
                  <td class="td-center">{{ $mk->kode_mk ?? '-' }}</td>
                  <td>{{ $mk->nama_mk ?? '-' }}</td>
                  <td class="td-center">{{ $mk->program_studi ?? '-' }}</td>
                  <td class="td-center">{{ $mk->sks ?? '-' }}</td>
                  <td class="td-center">{{ $mk->semester ?? '-' }}</td>

                  <td>
                    @if($pengampu && $pengampu->dosen)
                      <b>{{ $pengampu->dosen->nama }}</b><br>
                      <span style="color:var(--muted); font-size:12px;">
                        {{ $pengampu->dosen->kode_dosen }}
                      </span>
                    @else
                      <span style="color:var(--muted);">Belum di-set</span>
                    @endif
                  </td>

                  <td class="td-center">
                    <a class="btn btn-soft"
                       href="{{ route('admin.matakuliah.edit', ['mataKuliah' => $mk->id]) }}?semester={{ $semester ?? 1 }}&tahun_ajaran={{ $tahunAjaran ?? '' }}">
                      Edit
                    </a>

                    <form method="POST"
                          action="{{ route('admin.matakuliah.destroy', ['mataKuliah' => $mk->id]) }}"
                          style="display:inline-block;"
                          onsubmit="return confirm('Yakin mau hapus mata kuliah ini?\n\n{{ $mk->nama_mk }} ({{ $mk->kode_mk }})');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger" type="submit">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="td-center" style="color:var(--muted); padding:16px;">
                    Data mata kuliah belum ada.
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

@if(session('highlight_id'))

window.addEventListener('load', function () {

    const row = document.getElementById(
        'matakuliah-{{ session('highlight_id') }}'
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
