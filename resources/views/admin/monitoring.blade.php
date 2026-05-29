<!doctype html>
<html lang="id">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monitoring Jadwal</title>


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

      --green:#27ae60;
      --red:#eb5757;
      --filled:#baf7c1;
      --empty:#ffd3d3;

      /* ✅ Samakan dengan dosen.blade.php */
      --topbarH: 60px;
      --sidebarW: 220px;
    }

    *{ box-sizing:border-box; }

    html, body{
      margin:0;
      font-family: Arial, sans-serif;
      background:var(--bg);
      color:var(--text);
      overflow-x: hidden; /* cegah body melebar */
    }

    /* ✅ TOPBAR FIXED (konsisten) */
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
      background:#fff4e6;
      color:#e5861f;     
      border:1px solid rgba(229,134,31,.4);
      padding:8px 14px;
      border-radius:10px;
      cursor:pointer;
      font-weight:700;
      transition:0.2s;
    }

    /* ✅ SIDEBAR FIXED (konsisten) */
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

    /* ✅ CONTENT (konsisten) */
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

    .panel{
      border:1px solid var(--border);
      border-radius:12px;
      padding:14px;
      background:#fff;
    }

    .legend{
      display:flex;
      gap:14px;
      align-items:center;
      margin:8px 0 14px;
      font-size:13px;
      color:var(--muted);
      flex-wrap:wrap;
    }

    .dot{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px; }
    .dot.green{ background:var(--green); }
    .dot.red{ background:var(--red); }

    /* FILTER + IMPORT BAR */
    .toolbar{
      display:flex;
      gap:10px;
      align-items:center;
      flex-wrap:wrap;
      margin-top:10px;
    }

    select{
      width:240px;
      padding:10px;
      border-radius:10px;
      border:1px solid #ddd;
      outline:none;
      font-size:14px;
      background:#fff;
    }
    select:focus{
      border-color:var(--orange);
      box-shadow:0 0 0 4px rgba(229,134,31,.12);
    }

    input[type="file"]{
      padding:9px 10px;
      border-radius:10px;
      border:1px solid #ddd;
      background:#fff;
      font-size:13px;
      max-width: 320px;
    }

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

    .notice{
      margin-top:12px;
      padding:10px 12px;
      border-radius:10px;
      background:var(--orangeSoft);
      color:#7a4b10;
      font-size:13px;
      border:1px solid rgba(229,134,31,.25);
    }

    /* TABLE */
    .table-wrap{
      width:100%;
      overflow-x:auto;
      overflow-y:hidden;
      margin-top: 12px;
    }
    .minw{ min-width: 980px; }

    table{
      width:100%;
      border-collapse:collapse;
      overflow:hidden;
      border-radius:10px;
    }
    th, td{
      border:1px solid #ddd;
      padding:10px;
      vertical-align:top;
    }
    th{
      background:#f4f4f4;
      text-align:center;
      font-size:12px;
    }

    td.time{
      width:140px;
      font-weight:800;
      text-align:center;
      background:#fafafa;
      font-size:13px;
      white-space: nowrap;
    }

    .cell{
      border-radius:12px;
      padding:10px;
      min-height:70px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      text-align:center;
      gap:6px;
    }

    .cell.filled{ background:var(--filled); }
    .cell.empty{
      background:var(--empty);
      color:#555;
      font-weight:800;
    }

    .kelas{
      font-size:12px;
      font-weight:800;
      opacity:.9;
    }
    .mk{
      font-weight:900;
      font-size:13px;
      line-height: 1.2;
    }
    .kode{
      font-size:12px;
      opacity:.9;
      font-weight:700;
    }

    @media (max-width: 900px){
      :root{ --sidebarW: 190px; }
      .minw{ min-width: 900px; }
      select{ width: 100%; }
      input[type="file"]{ width: 100%; max-width: none; }
    }

    .modal{
    display:none;
    position:fixed;
    pointer-events: auto;
    inset:0;
    background:rgba(0,0,0,0.5);
    z-index:10000;
  }

.modal-content{
  background:#fff;
  width:400px;
  margin:60px auto;
  padding:20px;
  border-radius:12px;
  position:relative;
  z-index:10001;
  pointer-events: auto;
}

.form-group{
  margin-bottom:12px;
  display:flex;
  flex-direction:column;
}

.form-group label{
  font-weight:bold;
  margin-bottom:4px;
}

.form-group input,
.form-group select{
  padding:8px;
  border-radius:8px;
  border:1px solid #ddd;
}

.modal-actions{
  display:flex;
  gap:10px;
  margin-top:15px;
}

.btn-save{
  background:#e5861f;
  color:white;
  border:none;
  padding:8px 12px;
  border-radius:8px;
}

.btn-cancel{
  background:#ccc;
  border:none;
  padding:8px 12px;
  border-radius:8px;
}

.cell button{
  cursor:pointer;
  position:relative;
  z-index: 10;
}

.panel h4{
  margin-bottom:10px;
  font-size:14px;
}

.row{
  display:flex;
  gap:20px;
  flex-wrap:wrap;
  align-items:flex-start; 
}

.col{
  flex:1;
  min-width:280px;
  display:flex;
  flex-direction:column;
}

select, input{
  height:42px; 
}

label{
  display:block;
  font-size:12px;
  color:#777;
  margin-bottom:6px;
}

input, select{
  width:100%;
  padding:10px 12px;
  border-radius:10px;
  border:1px solid #ddd;
  margin-bottom:8px;
}

.success-box{
  margin-bottom:14px;
  background:#d4edda;
  color:#155724;
  padding:12px;
  border-radius:10px;
  border:1px solid #c3e6cb;
  font-size:14px;
}

.form-duo{
  display:flex;
  gap:20px;
  align-items:stretch;
  flex-wrap:wrap;
}

.form-duo .panel{
  flex:1;
  min-width:320px;
}

.new-highlight {
  animation: highlightFade 12s ease;
  background: #cce5ff !important;
}

@keyframes highlightFade {
  0%   { background: #66b3ff; }
  50%  { background: #99ccff; }
  100% { background: transparent; }
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
    <a class="active" href="{{ route('admin.monitoring') }}">Monitoring Jadwal</a>
    <a href="{{ route('admin.dosen.index') }}">Data Dosen</a>
    <a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a>
    <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
    <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
    <a href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
  </div>

  <div class="content">
    <div id="notif-success" style="display:none;" class="success-box"></div>

  @if(session('success'))
    <div class="success-box">
      {{ session('success') }}
    </div>
  @endif
        <!-- @if(session('success'))
      <div class="success">{{ session('success') }}</div>
    @endif -->
    <div class="card">
      <h3 class="page-title">Monitoring Jadwal Kuliah</h3>

      <div class="panel">
        <div class="legend">
          <span><b>Status Kelas:</b></span>
          <span><span class="dot green"></span>Masuk</span>
          <span><span class="dot red"></span>Kosong</span>
        </div>

        <div class="panel" style="margin-top:14px; margin-bottom:14px;">
        <h4>Kelola Periode Perkuliahan</h4>

        <div class="notice" style="margin-bottom:12px;">
          Periode ini digunakan untuk menghitung pertemuan minggu ini pada aplikasi dosen.
        </div>

        <form method="POST" action="{{ route('admin.periode.simpan') }}">
          @csrf

          <div class="row">
            <div class="col">
              <label>Tahun Ajaran</label>
              <input
                type="text"
                name="tahun_ajaran"
                value="{{ old('tahun_ajaran', $periodeAktif->tahun_ajaran ?? '2025/2026') }}"
                placeholder="Contoh: 2025/2026"
                required
              >

              <div class="col">
              <label>Status Periode</label>
              <select name="aktif" required>
                <option value="0" {{ old('aktif', $periodeAktif->aktif ?? 0) == 0 ? 'selected' : '' }}>
                  Belum Aktif
                </option>
                <option value="1" {{ old('aktif', $periodeAktif->aktif ?? 0) == 1 ? 'selected' : '' }}>
                  Aktif
                </option>
              </select>
            </div>
            </div>

            <div class="col">
              <label>Semester</label>
              <select name="semester" required>
                <option value="Ganjil" {{ old('semester', $periodeAktif->semester ?? '') == 'Ganjil' ? 'selected' : '' }}>
                  Ganjil
                </option>
                <option value="Genap" {{ old('semester', $periodeAktif->semester ?? '') == 'Genap' ? 'selected' : '' }}>
                  Genap
                </option>
              </select>
            </div>

            <div class="col">
              <label>Tanggal Mulai Perkuliahan</label>
              <input
                type="date"
                name="tanggal_mulai"
                value="{{ old('tanggal_mulai', $periodeAktif->tanggal_mulai ?? '') }}"
                required
              >
            </div>

            <div class="col">
              <label>Jumlah Pertemuan</label>
              <input
                type="number"
                name="jumlah_pertemuan"
                value="{{ old('jumlah_pertemuan', $periodeAktif->jumlah_pertemuan ?? 16) }}"
                min="1"
                max="20"
                required
              >
            </div>
          </div>

          <div class="actions" style="margin-top:10px;">
            <button class="btn btn-orange" type="submit">
              Simpan Periode Aktif
            </button>
          </div>
        </form>
      </div>

      @if(!$periodeAktif || !$periodeAktif->aktif)
        <div class="notice" style="background:#ffe6e6; color:#8a0000; border-color:#ffb3b3;">
          <b>Perhatian!</b> Periode perkuliahan belum aktif.
          Jadwal boleh sudah dimasukkan, tetapi dosen dan mahasiswa belum dapat menjalankan monitoring perkuliahan.
        </div>
      @endif

        <div class="toolbar">
          {{-- Filter hari --}}
          <form method="GET" action="{{ route('admin.monitoring') }}">
            <select name="hari" onchange="this.form.submit()">
              @foreach($daftarHari as $h)
                <option value="{{ $h }}" {{ $hari === $h ? 'selected' : '' }}>{{ $h }}</option>
              @endforeach
            </select>
          </form>
         
<div class="form-duo">
<div class="panel" style="margin-top:14px;">
  <h4>Tambah Jadwal Manual</h4>

  <form method="POST" action="{{ route('admin.jadwal.store') }}">
  @csrf

  <div class="row">

    <!-- KIRI -->
    <div class="col">

      <!-- PROGRAM STUDI -->
      <label>Program Studi</label>
      <select name="program_studi" id="program_studi" required>
        <option value="">-- Pilih Prodi --</option>
        @foreach($programStudis as $ps)
          <option value="{{ $ps }}">{{ $ps }}</option>
        @endforeach
      </select>

      <!-- MATAKULIAH -->
      <label style="margin-top:10px;">Mata Kuliah</label>
      <select name="pengampu_id" id="matkul" required>
        <option value="">-- Pilih Matkul --</option>
      </select>

      <!-- KELAS -->
      <label style="margin-top:10px;">Kelas</label>
      <select name="kelas" id="kelas_select" required>
        <option value="">-- Pilih Kelas --</option>
        @foreach($kelasList as $k)
          <option value="{{ $k }}">{{ $k }}</option>
        @endforeach
      </select>

    </div>

    <!-- KANAN -->
    <div class="col">

      <!-- WAKTU -->
      <label>Waktu</label>
      <select name="waktu_id" required>
        @foreach($waktusKosong as $w)
          <option value="{{ $w->id }}">
            {{ $w->hari }} ({{ \Carbon\Carbon::parse($w->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($w->jam_selesai)->format('H:i') }})
          </option>
        @endforeach
      </select>

      <!-- RUANGAN -->
      <label style="margin-top:10px;">Ruangan</label>
      <select name="ruangan_id" required>
        @foreach($ruangans as $r)
          <option value="{{ $r->id }}">{{ $r->kode_ruangan }}</option>
        @endforeach
      </select>

    </div>

  </div>

  <div class="actions">
    <button class="btn btn-orange" type="submit">Simpan Jadwal</button>
  </div>

  </form>
</div>

          <div class="panel" style="margin-top:14px;">
  <h4>Import Jadwal dari Excel</h4>

  <div style="
      background:#fff4e6;
      padding:10px 12px;
      border-radius:10px;
      font-size:13px;
      color:#7a4b10;
      border:1px solid rgba(229,134,31,.25);
      margin-bottom:12px;
  ">
    Upload file Excel untuk menambahkan banyak jadwal sekaligus.  
    Format: <b>xlsx / xls / csv</b>
  </div>

  <form method="POST" action="{{ route('admin.jadwal.import') }}" enctype="multipart/form-data">
    @csrf

    <div class="row">

      <div class="col">
        <label>Pilih File Excel</label>
        <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
      </div>

    </div>

    <div style="margin-top:12px;">
      <button type="submit" class="btn btn-orange">
        Import Jadwal
      </button>
    </div>

  </form>

  <div style="margin-top:10px; font-size:12px; color:#777;">
    Format kolom: kode_dosen, nama, program_studi, nidn, email, password
  </div>

</div>
</div>

          {{-- tombol bantu (opsional) --}}
          {{-- <a class="btn btn-soft" href="#">Download Template</a> --}}
        </div>

      @if($waktus->count() === 0 || $ruangans->count() === 0)
          <div class="notice">
            Data waktu/ruangan belum ada untuk hari <b>{{ $hari }}</b>. Silakan isi master data dulu.
          </div>
        @else
          <div class="table-wrap">
            <table class="minw">
              <thead>
                <tr>
                  <th>Waktu</th>
                  @foreach($ruangans as $r)
                    <th>
                      {{ $r->kode_ruangan }}
                      <br>
                      <span style="font-weight:normal;">{{ $r->nama_ruangan ?? '' }}</span>
                    </th>
                  @endforeach
                </tr>
              </thead>

              <tbody>
                @foreach($waktus as $w)
                  <tr>
                    <td class="time">
                      {{ \Carbon\Carbon::parse($w->jam_mulai)->format('H:i') }}
                      -
                      {{ \Carbon\Carbon::parse($w->jam_selesai)->format('H:i') }}
                    </td>

                    @foreach($ruangans as $r)
                      @php
                        $j = $matrix[$w->id][$r->id] ?? null;
                      @endphp

                     <td id="{{ $j ? 'jadwal-'.$j->id : '' }}">
                      @if($j)
                          <div class="cell filled">

                              <div class="kelas">{{ $j->kelas ?? '-' }}</div>
                              <div class="mk">{{ $j->nama_mk ?? '-' }}</div>
                              <div class="kode">{{ $j->kode_dosen ?? '-' }}</div>

                              <div style="margin-top:6px; display:flex; gap:5px;">
                                  
                                  <button type="button" onclick="editJadwal({{ $j->id }})"
                                      style="background:#ffffff;color:white;border:none;padding:5px 8px;border-radius:6px;">
                                      ✏️
                                  </button>

                                  <button onclick="hapusJadwal({{ $j->id }})"
                                      style="background:#ef4444;color:white;border:none;padding:5px 8px;border-radius:6px;">
                                      🗑️
                                  </button>

                              </div>

                          </div>
                      @else
                          <div class="cell empty">Kosong</div>
                      @endif
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

      </div>
    </div>
  </div>

  <!-- 🔥 PINDAHKAN MODAL KE SINI (INI FIX NYA) -->
  <div id="modalEdit" class="modal">

  <div class="modal-content">

  <h3>Edit Jadwal</h3>

  <input type="hidden" id="edit_id">

  <div class="form-group">
  <label>Kelas</label>
  <select id="edit_kelas">

<option value="">-- Pilih Kelas --</option>

</select>
  </div>

  <div class="form-group">
  <label>Program Studi</label>

  <select id="edit_prodi">
    <option value="">-- Pilih Prodi --</option>
    @foreach($programStudis as $ps)
      <option value="{{ $ps }}">{{ $ps }}</option>
    @endforeach
  </select>

  </div>

  <div class="form-group">
  <label>Mata Kuliah</label>
  <select id="edit_pengampu">
   <option value="">-- Pilih Matkul --</option>
  </select>
  </div>

  <div class="form-group">
  <label>Ruangan</label>
  <select id="edit_ruangan">
  @foreach($ruangans as $r)
  <option value="{{ $r->id }}">{{ $r->kode_ruangan }}</option>
  @endforeach
  </select>
  </div>

  <div class="form-group">
  <label>Waktu</label>
  <select id="edit_waktu">
  @foreach($allWaktus as $w)
  <option value="{{ $w->id }}">
  {{ $w->hari }} ({{ \Carbon\Carbon::parse($w->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($w->jam_selesai)->format('H:i') }})
  </option>
  @endforeach
  </select>
  </div>

  <div class="modal-actions">
  <button type="button" onclick="saveEdit()" class="btn btn-orange">
    Simpan
  </button>
  <button class="btn-cancel" onclick="closeModal()">Batal</button>
  </div>

  </div>
  </div>

  <script>
    const newJadwalId = "{{ session('new_jadwal_id') }}";
  </script>

  <!-- SCRIPT TETAP -->
  <script>

    document.addEventListener("click", function(e){
    console.log("CLICK:", e.target);
});

  function editJadwal(id) {

    fetch(`/admin/jadwal/${id}`)
    .then(res => res.json())
    .then(res => {

        const data = res.data;

        document.getElementById('edit_waktu').value = data.waktu_id;
        document.getElementById('edit_id').value = data.id;

        document.getElementById('edit_prodi').value = data.program_studi;
        filterEditDropdowns(
          data.waktu_id,
          data.program_studi,
          data.id,
          data.kelas,
          data.ruangan_id,
          data.pengampu_id
      );
        document.getElementById('edit_ruangan').value = data.ruangan_id;

        document.getElementById('modalEdit').style.display = 'block';
    });
}

  function closeModal() {
      document.getElementById('modalEdit').style.display = 'none';
  }

function saveEdit() {

    const id = document.getElementById('edit_id').value;

    fetch(`/admin/jadwal/${id}`, {
        method: 'POST', // 🔥 tetap POST
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin', // 🔥 WAJIB BIAR SESSION KEBACA
        body: JSON.stringify({
            _method: 'PUT',
            kelas: document.getElementById('edit_kelas').value,
            program_studi: document.getElementById('edit_prodi').value,
            pengampu_id: document.getElementById('edit_pengampu').value,
            ruangan_id: document.getElementById('edit_ruangan').value,
            waktu_id: document.getElementById('edit_waktu').value
        })
    })
    .then(res => res.json())
    .then(res => {
        console.log(res);

        if (res.success) {
            localStorage.setItem('success', res.message);
            localStorage.setItem('highlight_jadwal', id);
            location.reload();
        } else {
            alert(res.message || 'Gagal update');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error: ' + err.message);
    });
}

  function hapusJadwal(id) {
    if (!confirm('Yakin ingin menghapus jadwal ini?')) return;

    fetch(`/admin/jadwal/${id}`, {
        method: 'POST', // 🔥 pakai POST
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            _method: 'DELETE' // 🔥 Laravel trick
        })
    })
    .then(async res => {
        if (!res.ok) {
            throw new Error('HTTP ERROR ' + res.status);
        }
        return res.json();
    })
    .then(res => {
        if(res.success){
            localStorage.setItem('success', res.message);
            location.reload();
        } else {
            alert(res.message || 'Gagal hapus');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error: ' + err.message);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const msg = localStorage.getItem('success');
    if (msg) {
        const box = document.getElementById('notif-success');
        box.innerText = msg;
        box.style.display = 'block';

        localStorage.removeItem('success');
    }

    if (newJadwalId) {
        localStorage.setItem('highlight_jadwal', newJadwalId);
    }

    const targetId = localStorage.getItem('highlight_jadwal');

    if (targetId) {
        const el = document.getElementById('jadwal-' + targetId);

        if (el) {
            // scroll ke posisi
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // kasih efek
            el.classList.add('new-highlight');

            // hapus setelah 12 detik
            setTimeout(() => {
                el.classList.remove('new-highlight');
                localStorage.removeItem('highlight_jadwal');
            }, 2000);
        }
    }
});



  const pengampus = @json($pengampus);
  const jadwalTerpakai = @json($jadwalTerpakai);
  const ruanganSelect = document.querySelector('[name="ruangan_id"]');
  const waktuSelect = document.querySelector('[name="waktu_id"]');
  const allRuangan = @json($ruangans);
  const allWaktus = @json($allWaktus);
  const allKelas = @json($kelasList);

const kelasSelect = document.getElementById('kelas_select');

function filterDropdowns()
{
    const waktuId = waktuSelect.value;
    const prodi = document.getElementById('program_studi').value.trim().toLowerCase();

    // =========================
    // FILTER RUANGAN
    // =========================

    ruanganSelect.innerHTML = '';

    const ruanganTerpakai = jadwalTerpakai
        .filter(j => j.waktu_id == waktuId)
        .map(j => j.ruangan_id);

    const ruanganKosong =
        allRuangan.filter(r => !ruanganTerpakai.includes(r.id));

    ruanganKosong.forEach(r => {

        const option = document.createElement('option');

        option.value = r.id;
        option.textContent = r.kode_ruangan;

        ruanganSelect.appendChild(option);
    });

    // =========================
    // FILTER KELAS
    // =========================

    kelasSelect.innerHTML =
        '<option value="">-- Pilih Kelas --</option>';

    const kelasTerpakai = jadwalTerpakai
        .filter(j => j.waktu_id == waktuId)
        .map(j => j.kelas);

    const kelasKosong =
        @json($kelasList).filter(k => !kelasTerpakai.includes(k));

    kelasKosong.forEach(k => {

        const option = document.createElement('option');

        option.value = k;
        option.textContent = k;

        kelasSelect.appendChild(option);
    });

    // =========================
    // FILTER MATAKULIAH
    // =========================

    const matkulSelect = document.getElementById('matkul');

    matkulSelect.innerHTML =
        '<option value="">-- Pilih Matkul --</option>';

    const pengampuTerpakai = jadwalTerpakai
        .filter(j => j.waktu_id == waktuId)
        .map(j => j.pengampu_id);

    const filtered = pengampus.filter(p => {
        const prodiMatkul = (p.mata_kuliah?.program_studi ?? '').trim().toLowerCase();

        return prodiMatkul === prodi
            && !pengampuTerpakai.includes(p.id);
    });

    filtered.forEach(p => {

        const option = document.createElement('option');

        option.value = p.id;

        option.textContent =
            p.mata_kuliah.nama_mk +
            ' - ' +
            p.nama_pengampu;

        matkulSelect.appendChild(option);
    });
}

function namaPengampu(p) {
    let nama = p.dosen?.nama ?? '-';

    if (p.dosen2 && p.dosen2.nama) {
        nama += ' / ' + p.dosen2.nama;
    }

    return nama;
}

function loadEditMatkul(prodi, selectedPengampu = null)
{
    const matkulSelect = document.getElementById('edit_pengampu');

    matkulSelect.innerHTML =
        '<option value="">-- Pilih Matkul --</option>';

    const filtered = pengampus.filter(p => {
        return p.mata_kuliah.program_studi === prodi;
    });

    filtered.forEach(p => {

        const option = document.createElement('option');

        option.value = p.id;

        let namaDosen = p.dosen?.nama ?? '-';

        if (p.dosen2 && p.dosen2.nama) {
            namaDosen += ' / ' + p.dosen2.nama;
        }

        option.textContent =
            p.mata_kuliah.nama_mk + ' - ' + namaDosen;

        if (selectedPengampu == p.id) {
            option.selected = true;
        }

        matkulSelect.appendChild(option);
    });
}

document.getElementById('edit_prodi')
.addEventListener('change', function () {

    loadEditMatkul(this.value);

});

document.getElementById('program_studi')
.addEventListener('change', filterDropdowns);

waktuSelect.addEventListener('change', filterDropdowns);

document.addEventListener('DOMContentLoaded', function () {

    filterDropdowns();

}); 

function filterEditDropdowns(
    waktuId,
    prodi,
    currentJadwalId = null,
    selectedKelas = null,
    selectedRuangan = null,
    selectedPengampu = null
) {

    // =========================
    // AMBIL JADWAL LAIN
    // =========================

    const jadwalLain = jadwalTerpakai.filter(j => {

        return j.id != currentJadwalId
            && j.waktu_id == waktuId;
    });

    // =========================
    // FILTER KELAS
    // =========================

    const editKelas =
        document.getElementById('edit_kelas');

    editKelas.innerHTML =
        '<option value="">-- Pilih Kelas --</option>';

    const kelasTerpakai =
        jadwalLain.map(j => j.kelas);

    const kelasKosong =
        allKelas.filter(k => !kelasTerpakai.includes(k));

    kelasKosong.forEach(k => {

        const option = document.createElement('option');

        option.value = k;
        option.textContent = k;

        if (selectedKelas == k) {
            option.selected = true;
        }

        editKelas.appendChild(option);
    });

    // =========================
    // FILTER RUANGAN
    // =========================

    const editRuangan =
        document.getElementById('edit_ruangan');

    editRuangan.innerHTML = '';

    const ruanganTerpakai =
        jadwalLain.map(j => j.ruangan_id);

    const ruanganKosong =
        allRuangan.filter(r => !ruanganTerpakai.includes(r.id));

    ruanganKosong.forEach(r => {

        const option = document.createElement('option');

        option.value = r.id;
        option.textContent = r.kode_ruangan;

        if (selectedRuangan == r.id) {
            option.selected = true;
        }

        editRuangan.appendChild(option);
    });

    // =========================
    // FILTER MATAKULIAH
    // =========================

    const editPengampu =
        document.getElementById('edit_pengampu');

    editPengampu.innerHTML =
        '<option value="">-- Pilih Matkul --</option>';

    const pengampuTerpakai =
        jadwalLain.map(j => j.pengampu_id);

    const matkulKosong = pengampus.filter(p => {

        return p.mata_kuliah.program_studi === prodi
            && !pengampuTerpakai.includes(p.id);
    });

    matkulKosong.forEach(p => {

        const option = document.createElement('option');

        option.value = p.id;

        option.textContent =
            p.mata_kuliah.nama_mk +
            ' - ' +
            p.nama_pengampu;

        if (selectedPengampu == p.id) {
            option.selected = true;
        }

        editPengampu.appendChild(option);
    });
}

document.getElementById('edit_waktu')
.addEventListener('change', function () {

    const waktuId = this.value;

    const prodi =
        document.getElementById('edit_prodi').value;

    const currentJadwalId =
        document.getElementById('edit_id').value;

    const selectedKelas =
        document.getElementById('edit_kelas').value;

    const selectedPengampu =
        document.getElementById('edit_pengampu').value;

    filterEditDropdowns(
        waktuId,
        prodi,
        currentJadwalId,
        selectedKelas,
        null,
        selectedPengampu
    );

});

  </script>

  
</body>
</html>
