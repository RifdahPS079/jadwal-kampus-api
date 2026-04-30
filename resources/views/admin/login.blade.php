<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel - Sistem Penjadwalan Kampus</title>

  <style>
    :root{
      --orange:#E7831D;
      --orange-soft:#f0b37a;
      --text:#333;
      --muted:#7a7a7a;
    }

    *{ box-sizing: border-box; }
    body { margin:0; font-family: Arial, Helvetica, sans-serif; color: var(--text); }

    .bg {
      min-height: 100vh;
      background: url('{{ asset("images/kampusith.jpg") }}') center/cover no-repeat;
      position: relative;
      display:flex;
    }

    /* ✅ overlay jangan pernah menangkap klik */
    .bg::after{
      content:"";
      position:absolute; inset:0;
      background: rgba(255,255,255,0.55);
      pointer-events: none;     /* ✅ FIX: biar input bisa diklik */
      z-index: 0;
    }

    .wrap{
      position: relative;
      z-index: 1;               /* ✅ pastikan di atas overlay */
      width: 100%;
      min-height: 100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 24px;
    }

    .card{
      width: min(980px, 96vw);  /* ✅ lebih simetris di layar besar */
      background:#fff;
      border-radius: 18px;
      padding: 42px 64px;
      box-shadow: 0 18px 50px rgba(0,0,0,.18);
      border: 1px solid rgba(0,0,0,.06);
    }

    .header{
      display:flex;
      flex-direction: column;
      align-items:center;
      gap: 8px;
      margin-bottom: 18px;
    }

    .logo img{ width: 62px; height: 62px; object-fit: contain; }

    .title{
      font-weight: 700;
      color: var(--orange);
      margin: 0;
      font-size: 24px;
      line-height: 1.1;
      text-align:center;
    }
    .subtitle{
      margin: 0;
      color: var(--muted);
      font-size: 14px;
      text-align:center;
    }

    .form{
      max-width: 720px;         /* ✅ bikin form tengah & rapi */
      margin: 0 auto;
      margin-top: 22px;
    }

    .row{ margin-bottom: 18px; }
    label{ display:block; font-size: 14px; color:#777; margin-bottom: 8px; }

    input{
      width: 100%;
      padding: 14px 16px;
      border-radius: 8px;
      border: 1px solid rgba(231,131,29,.35);
      outline: none;
      font-size: 15px;
      background: #fff;
    }
    input:focus{
      border-color: var(--orange);
      box-shadow: 0 0 0 4px rgba(231,131,29,.12);
    }

    .btn-wrap{ display:flex; justify-content:center; margin-top: 14px; }
    button{
      width: 200px;
      height: 44px;
      border-radius: 999px;
      border: none;
      background: rgba(231,131,29,.55);
      color: #fff;
      font-weight: 800;
      font-size: 16px;
      cursor: pointer;
      box-shadow: 0 12px 22px rgba(231,131,29,.22);
    }
    button:hover{ background: rgba(231,131,29,.75); }

    .error{
      max-width: 720px;
      margin: 0 auto 14px;
      background: #ffe6e6;
      color: #b00020;
      padding: 10px 12px;
      border-radius: 10px;
      font-size: 13px;
    }
    .small-error{ color:#b00020; font-size:12px; margin-top:6px; }

    /* ✅ responsif biar tetap enak di laptop kecil */
    @media (max-width: 640px){
      .card{ padding: 28px 20px; }
      .title{ font-size: 20px; }
      button{ width: 170px; }
    }

    .logo-group{
      display:flex;
      align-items:center;
      gap:16px;
    }

    .logo-group img{
      width:60px;
      height:60px;
      object-fit:contain;
    }

    .password-wrap{
      position:relative;
    }

    .password-wrap input{
      padding-right:45px;
    }

    .toggle-password{
      position:absolute;
      right:12px;
      top:50%;
      transform:translateY(-50%);
      cursor:pointer;
      font-size:16px;
      color:#777;
    }
  </style>
</head>

<body>
<div class="bg">
  <div class="wrap">
    <div class="card">

      <div class="header">
        <div class="logo-group">
          <img src="{{ asset('images/logoith.jpg') }}" alt="Logo ITH">
          <img src="{{ asset('images/logoaplikasi1.png') }}" alt="Logo Aplikasi">
        </div>
        <h1 class="title">ADMIN SIHATI</h1>
        <p class="subtitle">(Sistem Informasi Jadwal ITH)</p>
      </div>

      @if($errors->has('login'))
        <div class="error">{{ $errors->first('login') }}</div>
      @endif

      <form class="form" method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <div class="row">
          <label>Username</label>
          <input name="username" value="{{ old('username') }}" placeholder="Masukkan username" autocomplete="username">
          @error('username') <div class="small-error">{{ $message }}</div> @enderror
        </div>

        <div class="password-wrap">
          <input type="password" id="password" name="password" placeholder="Masukkan password">
          <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <div class="btn-wrap">
          <button type="submit">MASUK</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "🙈";
    } else {
        input.type = "password";
        icon.textContent = "👁️";
    }
}
</script>
</body>
</html>
