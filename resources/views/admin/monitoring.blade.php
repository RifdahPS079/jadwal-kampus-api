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
}

.col{
  flex:1;
  min-width:280px;
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

        <div class="toolbar">
          {{-- Filter hari --}}
          <form method="GET" action="{{ route('admin.monitoring') }}">
            <select name="hari" onchange="this.form.submit()">
              @foreach($daftarHari as $h)
                <option value="{{ $h }}" {{ $hari === $h ? 'selected' : '' }}>{{ $h }}</option>
              @endforeach
            </select>
          </form>


<div class="panel" style="margin-top:14px;">
  <h4>Tambah Jadwal Manual</h4>

  <form method="POST" action="{{ route('admin.jadwal.store') }}">
    @csrf

    <div class="row">

      <!-- KIRI -->
      <div class="col">
        <label>Kelas</label>
        <input name="kelas" placeholder="Contoh: IK22B" required>

        <label style="margin-top:10px;">Mata Kuliah & Dosen</label>
        <select name="pengampu_id" required>
          @foreach($pengampus as $p)
            <option value="{{ $p->id }}">
              {{ $p->mataKuliah->nama_mk }} - {{ $p->dosen->nama }}
            </option>
          @endforeach
        </select>

        <label style="margin-top:10px;">Waktu</label>
        <select name="waktu_id" required>
          @foreach($waktus as $w)
            <option value="{{ $w->id }}">
              {{ $w->hari }} ({{ \Carbon\Carbon::parse($w->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($w->jam_selesai)->format('H:i') }})
            </option>
          @endforeach
        </select>
      </div>

      <!-- KANAN -->
      <div class="col">
        <label>Program Studi</label>
        <input name="program_studi" placeholder="Contoh: Ilmu Komputer" required>

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
      <button class="btn btn-soft" type="reset">Reset</button>
    </div>

  </form>
</div>

          {{-- Import jadwal --}}
          <form method="POST" action="{{ route('admin.jadwal.import') }}" enctype="multipart/form-data" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
            <button type="submit" class="btn btn-orange">Import Jadwal</button>
          </form>

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

                     <td>
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
  <input id="edit_kelas" placeholder="Contoh: IK22A">
  </div>

  <div class="form-group">
  <label>Program Studi</label>
  <input id="edit_prodi" placeholder="Ilmu Komputer">
  </div>

  <div class="form-group">
  <label>Mata Kuliah</label>
  <select id="edit_pengampu">
  @foreach($pengampus as $p)
  <option value="{{ $p->id }}">
  {{ $p->mataKuliah->nama_mk }} - {{ $p->dosen->nama }}
  </option>
  @endforeach
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
  @foreach($waktus as $w)
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

        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_kelas').value = data.kelas;
        document.getElementById('edit_prodi').value = data.program_studi;
        document.getElementById('edit_pengampu').value = data.pengampu_id;
        document.getElementById('edit_ruangan').value = data.ruangan_id;
        document.getElementById('edit_waktu').value = data.waktu_id;

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
});

  </script>

  
</body>
</html>
