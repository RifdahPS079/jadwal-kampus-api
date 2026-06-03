<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Riwayat Pertemuan</title>

  <style>
    :root{
      --orange:#e5861f;
      --orangeSoft:#fff4e6;
      --bg:#f6f6f6;
      --card:#ffffff;
      --border:#eeeeee;
      --text:#222;
      --muted:#777;
      --red:#eb5757;
      --green:#27ae60;
      --topbarH:60px;
      --sidebarW:220px;
    }

    *{ box-sizing:border-box; }

    body{
      margin:0;
      font-family:Arial, sans-serif;
      background:var(--bg);
      color:var(--text);
    }

    .topbar{
      position:fixed;
      top:0; left:0; right:0;
      height:var(--topbarH);
      background:var(--orange);
      color:#fff;
      padding:14px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      z-index:9999;
    }

    .logout-btn{
      background:#fff4e6;
      color:#e5861f;
      border:1px solid rgba(229,134,31,.4);
      padding:8px 14px;
      border-radius:10px;
      cursor:pointer;
      font-weight:700;
    }

    .sidebar{
      position:fixed;
      top:var(--topbarH);
      left:0;
      width:var(--sidebarW);
      height:calc(100vh - var(--topbarH));
      background:#fff;
      border-right:1px solid var(--border);
      padding:14px;
    }

    .sidebar a{
      display:block;
      padding:10px;
      color:var(--orange);
      text-decoration:none;
      border-radius:8px;
      margin-bottom:6px;
      font-size:14px;
    }

    .sidebar a.active{
      background:var(--orangeSoft);
      font-weight:700;
    }

    .content{
      margin-left:var(--sidebarW);
      padding:18px;
      padding-top:calc(var(--topbarH) + 18px);
    }

    .card{
      background:#fff;
      border:1px solid var(--border);
      border-radius:14px;
      padding:16px;
      margin-bottom:16px;
    }

    .title{
      margin:0 0 12px;
      font-size:18px;
    }

    .stats{
      display:grid;
      grid-template-columns:repeat(4, 1fr);
      gap:14px;
      margin-bottom:16px;
    }

    .stat-box{
      background:#fff;
      border:1px solid var(--border);
      border-radius:14px;
      padding:16px;
    }

    .stat-box small{
      color:var(--muted);
      font-size:12px;
    }

    .stat-box h2{
      margin:8px 0 0;
      font-size:24px;
    }

    .filter{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      margin-bottom:14px;
    }

    select, button{
      padding:10px 12px;
      border-radius:10px;
      border:1px solid #ddd;
      background:#fff;
    }

    .btn{
      background:var(--orange);
      color:white;
      border:none;
      font-weight:700;
      cursor:pointer;
    }

    table{
      width:100%;
      border-collapse:collapse;
      background:#fff;
      border-radius:12px;
      overflow:hidden;
    }

    th, td{
      border:1px solid #ddd;
      padding:10px;
      font-size:13px;
      vertical-align:top;
    }

    th{
      background:#f4f4f4;
      text-align:left;
    }

    .badge{
      padding:5px 9px;
      border-radius:999px;
      font-size:12px;
      font-weight:700;
      display:inline-block;
    }

    .pindah{
      background:#d7f8df;
      color:#1f7a3a;
    }

    .batal{
      background:#ffe0e0;
      color:#b00020;
    }

    .muted{
      color:#777;
      font-size:12px;
    }

    .table-wrap{
      overflow-x:auto;
    }

    .topbar-right{ display:flex; gap:12px; align-items:center; }

    @media(max-width:900px){
      .stats{ grid-template-columns:repeat(2, 1fr); }
      :root{ --sidebarW:190px; }
    }
  </style>
</head>

<body>

<<div class="topbar">
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
  <a href="{{ route('admin.matakuliah.index') }}">Mata Kuliah</a>
  <a href="{{ route('admin.ruangan_waktu.index') }}">Ruangan &amp; Waktu</a>
  <a class="active" href="{{ route('admin.riwayat.pertemuan') }}">Riwayat Pertemuan</a>
</div>

<div class="content">

  <div class="card">
    <h3 class="title">Riwayat Pertemuan</h3>

    <p class="muted">
      Halaman ini menampilkan riwayat kelas yang dibatalkan atau dipindahkan oleh dosen selama perkuliahan.
    </p>
  </div>

  <div class="stats">
    <div class="stat-box">
      <small>Semester Aktif</small>
      <h2>{{ $periodeAktif->semester ?? '-' }}</h2>
      <div class="muted">{{ $periodeAktif->tahun_ajaran ?? '-' }}</div>
    </div>

    <div class="stat-box">
      <small>Total Riwayat</small>
      <h2>{{ $totalRiwayat }}</h2>
    </div>

    <div class="stat-box">
      <small>Total Pindah</small>
      <h2>{{ $totalPindah }}</h2>
    </div>

    <div class="stat-box">
      <small>Total Batal</small>
      <h2>{{ $totalBatal }}</h2>
    </div>
  </div>

  <div class="card">
    <h3 class="title">Filter Riwayat</h3>

    <form method="GET" action="{{ route('admin.riwayat.pertemuan') }}" class="filter">
      <select name="status">
        <option value="">Semua Status</option>
        <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
      </select>

      <select name="pertemuan_ke">
        <option value="">Semua Pertemuan</option>
        @for($i = 1; $i <= 20; $i++)
          <option value="{{ $i }}" {{ request('pertemuan_ke') == $i ? 'selected' : '' }}>
            Pertemuan {{ $i }}
          </option>
        @endfor
      </select>

      <button class="btn" type="submit">Tampilkan</button>
    </form>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Tanggal Aksi</th>
            <th>Pertemuan</th>
            <th>Status</th>
            <th>Dosen</th>
            <th>Mata Kuliah</th>
            <th>Kelas</th>
            <th>Jadwal Lama</th>
            <th>Jadwal Baru</th>
            <th>Alasan</th>
          </tr>
        </thead>

        <tbody>
          @forelse($riwayats as $r)
            @php
              $jadwal = $r->jadwal;
              $pengampu = $jadwal?->pengampu;
              $mk = $pengampu?->mataKuliah;
              $dosen1 = $pengampu?->dosen;
              $dosen2 = $pengampu?->dosen2;

              $namaDosen = $dosen2
                  ? ($dosen1->nama ?? '-') . ' / ' . ($dosen2->nama ?? '-')
                  : ($dosen1->nama ?? '-');

              $waktuLama = $jadwal?->waktu;
              $ruangLama = $jadwal?->ruangan;

              $waktuBaru = $r->waktu;
              $ruangBaru = $r->ruangan;
            @endphp

            <tr>
              <td>
                {{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i') }}
              </td>

              <td>
                Pertemuan {{ $r->pertemuan_ke }}
              </td>

              <td>
                <span class="badge {{ $r->status == 'pindah' ? 'pindah' : 'batal' }}">
                  {{ strtoupper($r->status) }}
                </span>
              </td>

              <td>{{ $namaDosen }}</td>

              <td>{{ $mk->nama_mk ?? '-' }}</td>

              <td>{{ $jadwal->kelas ?? '-' }}</td>

              <td>
                {{ $waktuLama->hari ?? '-' }}<br>
                {{ $waktuLama ? \Carbon\Carbon::parse($waktuLama->jam_mulai)->format('H:i') : '-' }}
                -
                {{ $waktuLama ? \Carbon\Carbon::parse($waktuLama->jam_selesai)->format('H:i') : '-' }}
                <br>
                Ruangan: {{ $ruangLama->kode_ruangan ?? '-' }}
              </td>

              <td>
                @if($r->status == 'pindah')
                  {{ $waktuBaru->hari ?? '-' }}<br>
                  {{ $waktuBaru ? \Carbon\Carbon::parse($waktuBaru->jam_mulai)->format('H:i') : '-' }}
                  -
                  {{ $waktuBaru ? \Carbon\Carbon::parse($waktuBaru->jam_selesai)->format('H:i') : '-' }}
                  <br>
                  Ruangan: {{ $ruangBaru->kode_ruangan ?? '-' }}
                @else
                  -
                @endif
              </td>

              <td>{{ $r->alasan_batal ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align:center;">
                Belum ada riwayat pertemuan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <h3 class="title">Rekap Per Dosen</h3>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Dosen</th>
            <th>Total Pindah</th>
            <th>Total Batal</th>
            <th>Total Riwayat</th>
          </tr>
        </thead>

        <tbody>
          @forelse($rekapDosen as $namaDosen => $items)
            <tr>
              <td>{{ $namaDosen }}</td>
              <td>{{ $items->where('status', 'pindah')->count() }}</td>
              <td>{{ $items->where('status', 'batal')->count() }}</td>
              <td>{{ $items->count() }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align:center;">
                Belum ada data rekap dosen.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

</body>
</html>