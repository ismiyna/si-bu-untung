{{-- resources/views/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --sidebar:#181818;      /* warna sidebar (hitam keabu) */
      --sidebar-hover:#232323;
      --content-bg:#f5f5f5;   /* latar konten */
      --card-bg:#ffffff;      /* latar kartu */
      --text:#111;
      --muted:#777;
      --accent:#f0592b;       /* oranye */
      --green:#1f9d55;        /* hijau */
      --yellow:#f59e0b;       /* kuning */
      --radius:14px;
      --shadow:0 10px 25px rgba(0,0,0,.06);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0; font-family:'Poppins',system-ui,Segoe UI,Roboto,Helvetica,Arial; color:var(--text); background:var(--content-bg)}
    .layout{display:grid; grid-template-columns:260px 1fr; min-height:100vh}

    /* SIDEBAR */
    .sidebar{background:var(--sidebar); color:#fff; position:sticky; top:0; height:100vh; padding:16px 0}
    .logo{display:flex; align-items:center; gap:10px; padding:0 18px 18px; margin-bottom:8px; border-bottom:1px solid rgba(255,255,255,.06)}
    .logo .dot{width:12px; height:12px; border-radius:4px; background:var(--accent)}
    .menu{list-style:none; margin:0; padding:8px 10px}
    .menu > li{margin:4px 0}
    .menu a{display:flex; align-items:center; gap:10px; text-decoration:none; color:#ddd; padding:12px 16px; border-radius:10px}
    .menu a:hover,.menu a.active{background:var(--sidebar-hover); color:#fff}
    .menu .caret{margin-left:auto; transition:.2s}
    .submenu{display:none; padding-left:42px}
    .submenu a{padding:10px 12px; font-size:.92rem; color:#cfcfcf}
    .submenu a:hover{color:#fff}
    .open .submenu{display:block}
    .open .caret{transform:rotate(180deg)}

    /* CONTENT */
    .content{padding:28px 34px}
    .divider{height:1px; background:#e8e8e8; margin:10px 0 24px}

    /* Topbar refined */
    .topbar{display:flex; align-items:center; gap:16px; margin-bottom:18px}
    .page-title{font-size:28px; font-weight:800; letter-spacing:.3px}
    .topbar-right{margin-left:auto; display:flex; align-items:center; gap:12px}
    .role-badge{
      background:#111; color:#fff; font-weight:700; font-size:.9rem;
      padding:8px 12px; border-radius:999px; letter-spacing:.3px
    }
    .user-btn{width:40px; height:40px; border-radius:50%; display:grid; place-items:center; background:#111; color:#fff}

    /* CARDS */
    .grid{display:grid; grid-template-columns:repeat(2, minmax(280px,1fr)); gap:22px}
    @media (min-width:1100px){ .grid{grid-template-columns:repeat(2, 1fr)} }
    @media (max-width:640px){
      .page-title{font-size:22px}
      .role-badge{font-size:.85rem; padding:6px 10px}
      .grid{grid-template-columns:1fr}
    }
    .card{background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow); padding:22px;}
    .metric{display:flex; align-items:flex-start; gap:14px}
    .metric .icon{width:42px; height:42px; border-radius:12px; display:grid; place-items:center}
    .metric h4{margin:0; font-weight:600; color:var(--muted); font-size:.95rem}
    .metric .value{margin:6px 0 0; font-size:1.35rem; font-weight:800; letter-spacing:.3px}
    .icon-bag{background:#fff2ec; color:var(--accent)}
    .icon-people{background:#ecfff4; color:var(--green)}
    .icon-calendar{background:#fff3f3; color:#e63939}
    .icon-warning{background:#fff8e6; color:var(--yellow)}
  </style>
</head>
<body>

<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="logo">
      <span class="dot"></span>
      <strong>SI Bu Untung</strong>
    </div>

    <ul class="menu">
      <li><a href="{{ url('/dashboard') }}" class="active">
        {{-- grid icon --}}
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3H3v7h7V3Zm11 0h-7v7h7V3ZM10 14H3v7h7v-7Zm11 0h-7v7h7v-7Z"/></svg>
        Dashboard
      </a></li>

      <li class="has-sub">
        <a href="javascript:void(0)" onclick="toggleSub(this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6H4V4h16v2Zm0 5H4V9h16v2Zm0 5H4v-2h16v2Z"/></svg>
          Kelola Barang
          <svg class="caret" width="16" height="16" viewBox="0 0 24 24" fill="#bbb"><path d="M7 10l5 5 5-5z"/></svg>
        </a>
        <div class="submenu">
          <a href="{{ url('/tambah') }}">Tambah Barang</a>
          <a href="{{ url('/edit') }}">Edit Barang</a>
          <a href="{{ url('/hapus') }}">Hapus Barang</a>
        </div>
      </li>

      <li class="has-sub">
        <a href="javascript:void(0)" onclick="toggleSub(this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
          Laporan Barang
          <svg class="caret" width="16" height="16" viewBox="0 0 24 24" fill="#bbb"><path d="M7 10l5 5 5-5z"/></svg>
        </a>
        <div class="submenu">
          <a href="{{ url('/laporan/stok') }}">Stok</a>
          <a href="{{ url('/laporan/kadaluwarsa') }}">Kadaluwarsa</a>
        </div>
      </li>

      <li>
        <a href="{{ url('/laporan/penjualan') }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h2v18H3V3Zm16 0h2v18h-2V3ZM9 7h6v2H9V7Zm0 4h8v2H9v-2Zm0 4h5v2H9v-2Z"/></svg>
          Laporan Penjualan
        </a>
      </li>
    </ul>
  </aside>

  <!-- CONTENT -->
  <main class="content">

    {{-- TOPBAR --}}
    <div class="topbar">
      <div class="page-title">Dashboard</div>

      <div class="topbar-right">
        <span class="role-badge">
          @auth('staff')
            {{ ucwords(str_replace('_',' ', auth('staff')->user()->role ?? 'Staff')) }}
          @else
            Guest
          @endauth
        </span>

        <a class="user-btn" title="Akun">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="#fff" aria-hidden="true">
            <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12Zm0 2.4c-3.3 0-9.9 1.7-9.9 5v1.9h19.8v-2c0-3.2-6.6-4.9-9.9-4.9Z"/>
          </svg>
        </a>
      </div>
    </div>

    <div class="divider"></div>

    {{-- FORMATTER RUPIAH --}}
    @php
      $formatRupiah = fn($n) => 'Rp ' . number_format($n ?? 0, 0, ',', '.');
    @endphp

    {{-- GRID METRICS --}}
    <div class="grid">
      <!-- Total Penjualan (hari ini, sukses) -->
      <div class="card">
        <div class="metric">
          <div class="icon icon-bag">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M6 7V6a6 6 0 1 1 12 0v1h2v14H4V7h2Zm2 0h8V6a4 4 0 1 0-8 0v1Z"/>
            </svg>
          </div>
          <div>
            <h4>Total Penjualan</h4>
           <div class="value">{{ $formatRupiah($totalPenjualan ?? 0) }}</div>

          </div>
        </div>
      </div>

      <!-- Total Pengunjung (login hari ini) -->
      <div class="card">
        <div class="metric">
          <div class="icon icon-people">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M16 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-8 1a3 3 0 1 0-3-3 3 3 0 0 0 3 3Zm8 2c-3.3 0-10 1.7-10 5v2h20v-2c0-3.3-6.7-5-10-5Zm-8 0C4.7 14 0 15.7 0 19v2h6v-2c0-2 .9-3.1 2-3.9Z"/>
            </svg>
          </div>
          <div>
            <h4>Total Pengunjung</h4>
            <div class="value">{{ $totalPengunjung ?? 0 }}</div>
          </div>
        </div>
      </div>

      <!-- Hampir Kadaluwarsa (≤ 1 bulan) -->
      <div class="card">
        <div class="metric">
          <div class="icon icon-calendar">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2Zm13 7H4v11h16V9Z"/>
            </svg>
          </div>
          <div>
            <h4>Total Barang Hampir Kadaluwarsa</h4>
            <div class="value">{{ $hampirExpire ?? 0 }}</div>
          </div>
        </div>
      </div>

      <!-- Hampir Habis (stok < 20) -->
      <div class="card">
        <div class="metric">
          <div class="icon icon-warning">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M1 21h22L12 2 1 21Zm12-3h-2v-2h2v2Zm0-4h-2v-4h2v4Z"/>
            </svg>
          </div>
          <div>
            <h4>Total Barang Hampir Habis</h4>
            <div class="value">{{ $hampirHabis ?? 0 }}</div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  function toggleSub(el){
    const li = el.parentElement;
    li.classList.toggle('open');
  }
</script>
</body>
</html>
