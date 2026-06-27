<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Waktu</title>

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
}

/* ===== CARD ===== */
.card{
  background:#fff;
  border-radius:12px;
  padding:18px;
  border:1px solid #eee;
  margin-bottom:14px;
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

input{
  width:100%;
  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
  font-size:14px;
}

input:focus{
  border-color:#e5861f;
  box-shadow:0 0 0 3px rgba(229,134,31,.12);
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

<!-- TOPBAR -->
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
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a class="active" href="{{ route('admin.ruangan_waktu.index') }}">Ruangan & Waktu</a>
    <a href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- HEADER -->
    <div class="card">
      <div class="title">Edit Waktu</div>
      <div class="subtitle">
        Ubah jam perkuliahan sesuai kebutuhan.
      </div>
    </div>

    <!-- ERROR -->
   @if($errors->has('waktu'))

  <div class="alert-err">
      {{ $errors->first('waktu') }}
  </div>

@endif

    <!-- FORM -->
    <div class="card">
      <form method="POST" action="{{ route('admin.waktu.update', $waktu->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
          <div>
            <label>Jam Mulai</label>
            <input type="time" name="jam_mulai"
              value="{{ old('jam_mulai', \Carbon\Carbon::parse($waktu->jam_mulai)->format('H:i')) }}">
          </div>

          <div>
            <label>Jam Selesai</label>
            <input type="time" name="jam_selesai"
              value="{{ old('jam_selesai', \Carbon\Carbon::parse($waktu->jam_selesai)->format('H:i')) }}">
          </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px;">
          <button class="btn">Simpan Perubahan</button>
          <a class="btn soft" href="{{ route('admin.ruangan_waktu.index') }}">Kembali</a>
        </div>

      </form>
    </div>

  </div>
</div>

</body>
</html>