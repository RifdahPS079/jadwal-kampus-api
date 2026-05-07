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

.alert-err{
  background:#ffe5e5;
  border:1px solid #ffb3b3;
  color:#b00020;
  padding:12px;
  border-radius:10px;
  font-size:13px;
  margin-bottom:12px;
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

    @if(session('error'))

    <div class="alert-err">

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

          <select name="program_studi">

            <option value="">-- Pilih Program Studi --</option>

            <option value="Ilmu Komputer"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Ilmu Komputer' ? 'selected' : '' }}>
              Ilmu Komputer
            </option>

            <option value="Sistem Informasi"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Sistem Informasi' ? 'selected' : '' }}>
              Sistem Informasi
            </option>

            <option value="Matematika"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Matematika' ? 'selected' : '' }}>
              Matematika
            </option>

            <option value="Teknik Sipil"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Sipil' ? 'selected' : '' }}>
              Teknik Sipil
            </option>

            <option value="Sains Data"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Sains Data' ? 'selected' : '' }}>
              Sains Data
            </option>

            <option value="Teknologi Pangan"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknologi Pangan' ? 'selected' : '' }}>
              Teknologi Pangan
            </option>

            <option value="Bioteknologi"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Bioteknologi' ? 'selected' : '' }}>
              Bioteknologi
            </option>

            <option value="Teknik Arsitektur"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Arsitektur' ? 'selected' : '' }}>
              Teknik Arsitektur
            </option>

            <option value="Bisnis Digital"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Bisnis Digital' ? 'selected' : '' }}>
              Bisnis Digital
            </option>

            <option value="Sains Aktuaria"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Sains Aktuaria' ? 'selected' : '' }}>
              Sains Aktuaria
            </option>

            <option value="Teknik Mesin"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Mesin' ? 'selected' : '' }}>
              Teknik Mesin
            </option>

            <option value="Teknik Perkapalan"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Perkapalan' ? 'selected' : '' }}>
              Teknik Perkapalan
            </option>

            <option value="Teknik Elektro"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Elektro' ? 'selected' : '' }}>
              Teknik Elektro
            </option>

            <option value="Teknik Industri"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Industri' ? 'selected' : '' }}>
              Teknik Industri
            </option>

            <option value="Teknik Lingkungan"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Lingkungan' ? 'selected' : '' }}>
              Teknik Lingkungan
            </option>

            <option value="Teknik Sistem Energi"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Sistem Energi' ? 'selected' : '' }}>
              Teknik Sistem Energi
            </option>

            <option value="Teknik Metalurgi"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Metalurgi' ? 'selected' : '' }}>
              Teknik Metalurgi
            </option>

            <option value="Teknik Robotika & Kecerdasan Buatan"
              {{ old('program_studi', $mataKuliah->program_studi) == 'Teknik Robotika & Kecerdasan Buatan' ? 'selected' : '' }}>
              Teknik Robotika & Kecerdasan Buatan
            </option>

          </select>

      </div>

         <div>
            <label>SKS</label>
            <select name="sks">

                <option value="1"
                    {{ old('sks', $mataKuliah->sks) == 1 ? 'selected' : '' }}>
                    1 SKS
                </option>

                <option value="2"
                    {{ old('sks', $mataKuliah->sks) == 2 ? 'selected' : '' }}>
                    2 SKS
                </option>

                <option value="3"
                    {{ old('sks', $mataKuliah->sks) == 3 ? 'selected' : '' }}>
                    3 SKS
                </option>

                <option value="4"
                    {{ old('sks', $mataKuliah->sks) == 4 ? 'selected' : '' }}>
                    4 SKS
                </option>

                <option value="5"
                    {{ old('sks', $mataKuliah->sks) == 5 ? 'selected' : '' }}>
                    5 SKS
                </option>

            </select>

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

    <select name="semester">

        @for($i = 1; $i <= 14; $i++)

            <option value="{{ $i }}"
                {{ old('semester', $mataKuliah->semester) == $i ? 'selected' : '' }}>

                Semester {{ $i }}

            </option>

        @endfor

    </select>

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