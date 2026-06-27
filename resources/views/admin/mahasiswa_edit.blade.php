<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Mahasiswa</title>

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
  width:270px;
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
  font-size:14px;
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
  overflow-x:hidden; /* ⬅️ penting: cegah keluar */
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
  font-size:18px; /* ⬅️ diperkecil */
  font-weight:700;
  margin-bottom:4px;
}

.subtitle{
  font-size:12px;
  color:#777;
}

/* ===== FORM GRID ===== */
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

/* ===== FULL WIDTH FIELD ===== */
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

/* ===== RESPONSIVE ===== */
@media(max-width:900px){
  .row{
    grid-template-columns:1fr;
  }
  .full{
    grid-column: span 1;
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
  <div style="display:flex; gap:12px; align-items:center;">
    <a class="notif-icon" href="{{ route('admin.notifikasi') }}" title="Permohonan perubahan jadwal">
    🔔
    @if(($jumlahPermohonanMenunggu ?? 0) > 0)
      <span class="notif-count">{{ $jumlahPermohonanMenunggu }}</span>
    @endif
  </a>
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
    <a href="{{ route('admin.monitoring') }}">Penyusunan & Monitoring Jadwal</a>
    <a href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a class="active" href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
    <a href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- HEADER CARD -->
    <div class="card">
      <div class="title">Edit Mahasiswa</div>
      <div class="subtitle">Ubah data, password boleh dikosongkan jika tidak ingin mengganti.</div>
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
      <form method="POST" action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
          <div>
            <label>NIM (9 angka)</label>
            <input
                type="text"
                name="nim"
                value="{{ old('nim', $mahasiswa->nim) }}"
                maxlength="9"
                minlength="9"
                inputmode="numeric"
                pattern="[0-9]{9}"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            >
          </div>
          <div>
            <label>Nama Mahasiswa</label>
            <input name="nama" value="{{ old('nama', $mahasiswa->nama) }}">
          </div>
        </div>

        <div class="row">
          <div>
            <label>Program Studi</label>
            <select name="program_studi">

              <option value="">-- Pilih Program Studi --</option>

              <option value="Sistem Informasi"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Ilmu Komputer' ? 'selected' : '' }}>
                Ilmu Komputer
              </option>

              <option value="Sistem Informasi"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Sistem Informasi' ? 'selected' : '' }}>
                Sistem Informasi
              </option>

              <option value="Matematika"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Matematika' ? 'selected' : '' }}>
                Matematika
              </option>

              <option value="Teknik Sipil"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Sipil' ? 'selected' : '' }}>
                Teknik Sipil
              </option>

              <option value="Sains Data"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Sains Data' ? 'selected' : '' }}>
                Sains Data
              </option>

              <option value="Teknologi Pangan"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknologi Pangan' ? 'selected' : '' }}>
                Teknologi Pangan
              </option>

              <option value="Bioteknologi"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Bioteknologi' ? 'selected' : '' }}>
                Bioteknologi
              </option>

              <option value="Teknik Arsitektur"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Arsitektur' ? 'selected' : '' }}>
                Teknik Arsitektur
              </option>

              <option value="Bisnis Digital"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Bisnis Digital' ? 'selected' : '' }}>
                Bisnis Digital
              </option>

              <option value="Sains Aktuaria"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Sains Aktuaria' ? 'selected' : '' }}>
                Sains Aktuaria
              </option>

              <option value="Teknik Mesin"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Mesin' ? 'selected' : '' }}>
                Teknik Mesin
              </option>

              <option value="Teknik Perkapalan"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Perkapalan' ? 'selected' : '' }}>
                Teknik Perkapalan
              </option>

              <option value="Teknik Elektro"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Elektro' ? 'selected' : '' }}>
                Teknik Elektro
              </option>

              <option value="Teknik Industri"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Industri' ? 'selected' : '' }}>
                Teknik Industri
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Lingkungan' ? 'selected' : '' }}>
                Teknik Lingkungan
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Sistem Energi' ? 'selected' : '' }}>
                Teknik Sistem Energi
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Metalurgi' ? 'selected' : '' }}>
                Teknik Metalurgi
              </option>

              <option value="Teknik Lingkungan"
                {{ old('program_studi', $mahasiswa->program_studi) == 'Teknik Robotika & Kecerdasan Buatan' ? 'selected' : '' }}>
                Teknik Robotika & Kecerdasan Buatan
              </option>

            </select>
          </div>
          <div>
            <label>Kelas</label>
            <input name="kelas" value="{{ old('kelas', $mahasiswa->kelas) }}">
          </div>
        </div>

        <div class="row">
          <div>
            <label>Angkatan</label>
            <input name="angkatan" value="{{ old('angkatan', $mahasiswa->angkatan) }}">
          </div>
          <div>
            <label>Email</label>
            <input type="email" name="email"
              value="{{ old('email', $mahasiswa->email) }}">
          </div>
        </div>

        <div class="row">
          <div class="full">
            <label>Password Baru (opsional)</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti">
          </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px;">
          <button class="btn">Simpan Perubahan</button>
          <a class="btn soft" href="{{ route('admin.mahasiswa.index') }}">Kembali</a>
        </div>

      </form>
    </div>

  </div>
</div>

</body>
</html>