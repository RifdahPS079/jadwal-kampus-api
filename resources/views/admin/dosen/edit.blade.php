<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Dosen</title>

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

/* ===== HEADER TEXT ===== */
.title{
  font-size:18px;
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
  font-size:13px;
  color:#666;
  margin-bottom:6px;
  display:block;
}

input,
select{
  width:100%;
  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
  font-size:14px;
  background:#fff;
}

input:focus,
select:focus{
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

/* ===== ERROR ===== */
.alert-err{
  background:#ffecec;
  border:1px solid #ffbdbd;
  color:#a80000;
  padding:12px;
  border-radius:12px;
  margin-bottom:12px;
}

/* ===== SMALL ERROR ===== */
.small-error{
  color:#b00020;
  font-size:12px;
  margin-top:4px;
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
    <a class="active" href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
    <a href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- HEADER -->
    <div class="card">
      <div class="title">Edit Dosen</div>
      <div class="subtitle">Ubah data dosen, password boleh dikosongkan jika tidak ingin mengganti.</div>
    </div>

    <!-- ERROR -->
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
      <form method="POST" action="{{ route('admin.dosen.update', $dosen->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
          <div>
            <label>Kode Dosen</label>
            <input name="kode_dosen" value="{{ old('kode_dosen', $dosen->kode_dosen) }}">
            @error('kode_dosen') <div class="small-error">{{ $message }}</div> @enderror
          </div>

          <div>
            <label>Nama Dosen</label>
            <input name="nama" value="{{ old('nama', $dosen->nama) }}">
            @error('nama') <div class="small-error">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="row">
          <div>
          <label>NIDN (10 angka)</label>

          <input
              type="text"
              name="nidn"
              value="{{ old('nidn', $dosen->nidn) }}"
              maxlength="10"
              minlength="10"
              inputmode="numeric"
              pattern="[0-9]{10}"
              oninput="this.value = this.value.replace(/[^0-9]/g, '')"
          >

          @error('nidn')
          <div class="small-error">{{ $message }}</div>
          @enderror
          </div>

          <div>
           <label>Program Studi</label>

            <select name="program_studi">

              <option value="">-- Pilih Program Studi --</option>

              <option value="Ilmu Komputer"
                {{ old('program_studi', $dosen->program_studi) == 'Ilmu Komputer' ? 'selected' : '' }}>
                Ilmu Komputer
              </option>

              <option value="Sistem Informasi"
                {{ old('program_studi', $dosen->program_studi) == 'Sistem Informasi' ? 'selected' : '' }}>
                Sistem Informasi
              </option>

              <option value="Matematika"
                {{ old('program_studi', $dosen->program_studi) == 'Matematika' ? 'selected' : '' }}>
                Matematika
              </option>

              <option value="Teknik Sipil"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Sipil' ? 'selected' : '' }}>
                Teknik Sipil
              </option>

              <option value="Sains Data"
                {{ old('program_studi', $dosen->program_studi) == 'Sains Data' ? 'selected' : '' }}>
                Sains Data
              </option>

              <option value="Teknologi Pangan"
                {{ old('program_studi', $dosen->program_studi) == 'Teknologi Pangan' ? 'selected' : '' }}>
                Teknologi Pangan
              </option>

              <option value="Bioteknologi"
                {{ old('program_studi', $dosen->program_studi) == 'Bioteknologi' ? 'selected' : '' }}>
                Bioteknologi
              </option>

              <option value="Teknik Arsitektur"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Arsitektur' ? 'selected' : '' }}>
                Teknik Arsitektur
              </option>

              <option value="Bisnis Digital"
                {{ old('program_studi', $dosen->program_studi) == 'Bisnis Digital' ? 'selected' : '' }}>
                Bisnis Digital
              </option>

              <option value="Sains Aktuaria"
                {{ old('program_studi', $dosen->program_studi) == 'Sains Aktuaria' ? 'selected' : '' }}>
                Sains Aktuaria
              </option>

              <option value="Teknik Mesin"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Mesin' ? 'selected' : '' }}>
                Teknik Mesin
              </option>

              <option value="Teknik Perkapalan"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Perkapalan' ? 'selected' : '' }}>
                Teknik Perkapalan
              </option>

              <option value="Teknik Elektro"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Elektro' ? 'selected' : '' }}>
                Teknik Elektro
              </option>

              <option value="Teknik Industri"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Industri' ? 'selected' : '' }}>
                Teknik Industri
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Lingkungan' ? 'selected' : '' }}>
                Teknik Lingkungan
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Sistem Energi' ? 'selected' : '' }}>
                Teknik Sistem Energi
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Metalurgi' ? 'selected' : '' }}>
                Teknik Metalurgi
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $dosen->program_studi) == 'Teknik Robotika & Kecerdasan Buatan' ? 'selected' : '' }}>
                Teknik Robotika & Kecerdasan Buatan
              </option>

            </select>

            @error('program_studi')
            <div class="small-error">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $dosen->email) }}">
            @error('email') <div class="small-error">{{ $message }}</div> @enderror
          </div>

          <div>
            <label>Password (opsional)</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin ganti password">
            @error('password') <div class="small-error">{{ $message }}</div> @enderror
          </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px;">
          <button class="btn">Simpan Perubahan</button>
          <a class="btn soft" href="{{ route('admin.dosen.index') }}">Kembali</a>
        </div>

      </form>
    </div>

  </div>
</div>

</body>
</html>