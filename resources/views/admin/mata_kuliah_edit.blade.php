<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Mata Kuliah</title>

<style>
*{ box-sizing:border-box; }

body{
  margin:0;
  font-family: Arial, sans-serif;
  background:#f6f6f6;
}

/* ===== TOPBAR ===== */
.topbar{
  background:#e5861f;
  color:#fff;
  padding:14px 18px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

/* ===== LAYOUT ===== */
.layout{
  display:flex;
  min-height: calc(100vh - 52px);
}

/* ===== SIDEBAR ===== */
.sidebar{
  width:220px;
  background:#fff;
  border-right:1px solid #eee;
  padding:14px;
}

.sidebar a{
  display:block;
  padding:10px;
  color:#e5861f;
  text-decoration:none;
  border-radius:8px;
  margin-bottom:6px;
}

.sidebar a.active{
  background:#fff4e6;
  font-weight:bold;
}

/* ===== CONTENT ===== */
.content{
  flex:1;
  padding:18px;
  max-width:100%;
  overflow-x:hidden;
}

/* ===== CARD ===== */
.card{
  background:#fff;
  border-radius:12px;
  padding:18px;
  border:1px solid #eee;
  margin-bottom:14px;
  width:100%;
}

/* ===== HEADER ===== */
.title{
  font-size:20px;
  font-weight:700;
  margin-bottom:4px;
}

.subtitle{
  font-size:12px;
  color:#777;
}

/* ===== GRID ===== */
.row{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:14px;
  margin-top:12px;
}

/* ===== INPUT ===== */
label{
  display:block;
  font-size:13px;
  color:#666;
  margin-bottom:6px;
}

input, select{
  width:100%;
  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
  font-size:14px;
}

input:focus, select:focus{
  border-color:#e5861f;
  box-shadow:0 0 0 3px rgba(229,134,31,.12);
}

/* ===== FULL WIDTH ===== */
.full{
  grid-column: span 2;
}

/* ===== BUTTON ===== */
.btn{
  background:#e5861f;
  color:#fff;
  border:0;
  padding:10px 14px;
  border-radius:10px;
  cursor:pointer;
  font-weight:700;
  text-decoration:none;
}

.btn.soft{
  background:#fff4e6;
  color:#e5861f;
  border:1px solid rgba(229,134,31,.35);
}

/* ===== RESPONSIVE ===== */
@media(max-width:900px){
  .row{
    grid-template-columns:1fr;
  }
  .full{
    grid-column: span 1;
  }
}
</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
  <div><b>Web Admin Penjadwalan Perkuliahan</b></div>
  <div style="display:flex; gap:12px; align-items:center;">
    <span>Admin</span>
    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button class="btn soft">Logout</button>
    </form>
  </div>
</div>

<div class="layout">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <a href="{{ route('admin.monitoring') }}">Monitoring Jadwal</a>
    <a href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a class="active" href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan & Waktu</a>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- HEADER -->
    <div class="card">
      <div class="title">Edit Mata Kuliah</div>
      <div class="subtitle">
        Ubah data mata kuliah dan dosen pengampu.
      </div>
    </div>

    <!-- FORM -->
    <div class="card">
      <form method="POST" action="{{ route('admin.matakuliah.update', $mataKuliah->id) }}">
        @csrf
        @method('PUT')

        <!-- ROW 1 -->
        <div class="row">
          <div>
            <label>Kode MK</label>
            <input name="kode_mk" value="{{ old('kode_mk', $mataKuliah->kode_mk) }}">
          </div>

          <div>
            <label>Nama MK</label>
            <input name="nama_mk" value="{{ old('nama_mk', $mataKuliah->nama_mk) }}">
          </div>
        </div>

        <!-- ROW 2 -->
        <div class="row">
          <div>
            <label>Program Studi</label>
            <input name="program_studi" value="{{ old('program_studi', $mataKuliah->program_studi) }}">
          </div>

          <div>
            <label>SKS</label>
            <input type="number" name="sks" value="{{ old('sks', $mataKuliah->sks) }}">
          </div>
        </div>

        <!-- ROW 3 -->
        <div class="row">
          <div>
            <label>Dosen Pengampu</label>
            <select name="dosen_id">
              @foreach($dosens as $d)
                <option value="{{ $d->id }}"
                  {{ old('dosen_id', optional($pengampu)->dosen_id) == $d->id ? 'selected' : '' }}>
                  {{ $d->nama }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label>Semester</label>
            <input type="number" name="semester" value="{{ old('semester', $semester) }}">
          </div>
        </div>

        <!-- FULL -->
        <div class="row">
          <div class="full">
            <label>Tahun Ajaran</label>
            <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}">
          </div>
        </div>

        <!-- BUTTON -->
        <div style="margin-top:16px; display:flex; gap:10px;">
          <button class="btn">Simpan Perubahan</button>
          <a class="btn soft" href="{{ route('admin.matakuliah.index') }}">Kembali</a>
        </div>

      </form>
    </div>

  </div>
</div>

</body>
</html>