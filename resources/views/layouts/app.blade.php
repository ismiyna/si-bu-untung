{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

  <style>
    /* ========= THEME (netral, tanpa kebiruan) ========= */
    :root{
      --sb-accent:#7c5cff;           /* boleh tetap, jarang dipakai */
      --sb-accent-2:#5aa3ff;
      --sb-bg:#f7f8fc;               /* body bg TERANG */
      --sb-content:#ffffff;          /* konten bg PUTIH */
      --sb-text:#0f1421;             /* teks utama */
      --sb-muted:#7a839a;            /* teks sekunder */
      --sb-side:#121212;             /* sidebar netral gelap */
      --sb-side-2:#121212;           /* tanpa gradasi biru */
      --sb-hover:#1e1e1e;            /* hover netral */
      --sb-ring:#1f2023;             /* border halus */
      --sb-soft:rgba(124,92,255,.12);
      --sb-radius:16px;
      --sb-shadow:0 20px 40px rgba(5,10,25,.08);
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:'Poppins',system-ui,Segoe UI,Roboto,Helvetica,Arial;
      background:var(--sb-bg);       /* <— terang/putih, bukan biru gelap */
      color:var(--sb-text);
    }

    /* ========= LAYOUT & COLLAPSE (no JS) ========= */
    .sb-nav-toggle{display:none}
    .sb-layout{
      display:grid;grid-template-columns:300px 1fr;min-height:100vh;
      transition:grid-template-columns .22s ease;
    }
    .sb-nav-toggle:checked + .sb-layout{grid-template-columns:96px 1fr}

    /* ========= SIDEBAR ========= */
    .sb-sidebar{
      position:sticky;top:0;height:100vh;overflow:auto;
      background:var(--sb-side);           /* <— solid, tanpa radial/linear gradient */
      border-right:1px solid var(--sb-ring);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.04);
      color:#e9ebf6;padding:18px 0 12px;
    }
    .sb-sidebar::-webkit-scrollbar{width:8px}
    .sb-sidebar::-webkit-scrollbar-thumb{background:#2a2a2a;border-radius:10px}

    /* Brand */
    .sb-brand{
      display:flex;align-items:center;justify-content:space-between;
      gap:10px;padding:0 16px 16px;margin-bottom:12px;
      border-bottom:1px solid rgba(255,255,255,.06)
    }
    .sb-brand-left{display:flex;align-items:center;gap:12px}
    .sb-dot{
      width:12px;height:12px;border-radius:4px;
      background:#9aa0a6;            /* <— netral (hilangkan aksen ungu) */
      box-shadow:none;
    }
    .sb-brand strong{letter-spacing:.2px}

    /* Toggle button */
    .sb-toggle{
      appearance:none;border:1px solid #2a2a2a;background:#1a1a1a;
      width:44px;height:34px;border-radius:12px;display:grid;place-items:center;cursor:pointer;
      transition:transform .15s ease, background .15s ease;
    }
    .sb-toggle:hover{background:#202020;transform:translateY(-1px)}
    .sb-toggle svg{opacity:.92}

    /* Section label */
    .sb-section{font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#a6afc8;margin:10px 22px 8px}

    /* Menu */
    .sb-menu{list-style:none;margin:0;padding:8px 10px}
    .sb-menu > li{margin:6px 0}

    /* Pill link */
    .sb-pill{
      position:relative;display:flex;align-items:center;gap:12px;
      padding:12px 14px;border-radius:14px;text-decoration:none;color:#dfe3f7;
      transition:.22s ease;border:1px solid rgba(255,255,255,.03);
      background:linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01));
      box-shadow:inset 0 -1px 0 rgba(255,255,255,.04);
      overflow:hidden;
    }
    .sb-pill:hover{background:var(--sb-hover);border-color:#2a2a2a;translate:0 -1px}
    .sb-icon{
      width:36px;height:36px;border-radius:12px;display:grid;place-items:center;flex:0 0 36px;
      background:#1f1f1f;             /* <— bukan #12183a (biru) */
      border:1px solid #2a2a2a;        /* <— bukan #243063 (biru) */
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.04)
    }
    .sb-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .sb-pill.active{
      background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.04));
      border-color:#2a2a2a;color:#fff;
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.06);
    }
    .sb-pill.active .sb-icon{background:#222;border-color:#2a2a2a}
    .sb-pill.active::before{
      content:"";position:absolute;left:-10px;top:8px;bottom:8px;width:4px;border-radius:8px;
      background:#3a3a3a;box-shadow:0 0 0 6px rgba(255,255,255,.02)
    }

    /* details/summary submenu */
    details.sb-acc{border-radius:14px}
    details.sb-acc > summary{
      list-style:none;cursor:pointer;display:flex;align-items:center;gap:12px;
      padding:12px 14px;border-radius:14px;color:#e1e6ff;border:1px solid rgba(255,255,255,.04);
      background:linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01));
      transition:.22s ease
    }
    details.sb-acc > summary:hover{background:var(--sb-hover);border-color:#2a2a2a}
    details.sb-acc[open] > summary{background:#1a1a1a;border-color:#2a2a2a}
    .sb-caret{margin-left:auto;transition:transform .2s ease;opacity:.92}
    details.sb-acc[open] .sb-caret{transform:rotate(180deg)}

    .sb-submenu{padding:6px 0 12px 58px}
    .sb-submenu a{
      display:flex;align-items:center;gap:10px;text-decoration:none;color:#cfd5f1;padding:9px 0;border-radius:10px;
    }
    .sb-submenu a:hover{color:#fff}
    .sb-submenu a.active{color:#fff;font-weight:600}
    .sb-dotmini{width:6px;height:6px;border-radius:50%;background:#3a3a3a}
    .sb-submenu a:hover .sb-dotmini{background:#9aa0a6}

    /* ========= CONTENT (PUTIH solid) ========= */
    .sb-content{
      padding:28px 34px;
      background:var(--sb-content);   /* <— putih solid */
    }
    .sb-card{
      background:#fff;border:1px solid #e8eaf5;border-radius:var(--sb-radius);
      padding:18px;box-shadow:var(--sb-shadow)
    }

    /* ========= COLLAPSE STATES ========= */
    .sb-nav-toggle:checked + .sb-layout .sb-brand strong,
    .sb-nav-toggle:checked + .sb-layout .sb-section,
    .sb-nav-toggle:checked + .sb-layout .sb-label,
    .sb-nav-toggle:checked + .sb-layout .sb-badge{display:none}

    .sb-nav-toggle:checked + .sb-layout .sb-submenu{padding-left:18px}
    .sb-nav-toggle:checked + .sb-layout .sb-pill{justify-content:center}

    /* ========= RESPONSIVE ========= */
    @media (max-width: 1024px){
      .sb-layout{grid-template-columns:96px 1fr}
      .sb-brand strong,.sb-section,.sb-label,.sb-badge{display:none}
      .sb-submenu{padding-left:18px}
      .sb-pill{justify-content:center}
    }
  </style>
</head>
<body>

<input type="checkbox" id="sb-collapse" class="sb-nav-toggle">
<div class="sb-layout">
  <!-- SIDEBAR -->
  <aside class="sb-sidebar">
    <div class="sb-brand">
      <div class="sb-brand-left">
        <span class="sb-dot"></span>
        <strong>SI Bu Untung</strong>
      </div>
      <label class="sb-toggle" for="sb-collapse" title="Collapse / Expand">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#cfd3ff"><path d="M4 7h16v2H4V7Zm0 4h10v2H4v-2Zm0 4h16v2H4v-2Z"/></svg>
      </label>
    </div>

    @php
      $isDashboard = request()->is('dashboard');
      $user = auth('staff')->user() ?? auth('pelanggan')->user();
      $openKelola = request()->is('tambah') || request()->is('edit') || request()->is('hapus');
      $openLaporan = request()->is('laporan/stok') || request()->is('laporan/kadaluwarsa');
    @endphp

    <div class="sb-section">Menu</div>
    <ul class="sb-menu">
      <li>
        <a href="{{ url('/dashboard') }}" class="sb-pill {{ $isDashboard ? 'active' : '' }}">
          <span class="sb-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3H3v7h7V3Zm11 0h-7v7h7V3ZM10 14H3v7h7v-7Zm11 0h-7v7h7v-7Z"/></svg>
          </span>
          <span class="sb-label">Dashboard</span>
        </a>
      </li>

      <li>
        <details class="sb-acc" {{ $openKelola ? 'open' : '' }}>
          <summary>
            <span class="sb-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6H4V4h16v2Zm0 5H4V9h16v2Zm0 5H4v-2h16v2Z"/></svg>
            </span>
            <span class="sb-label">Kelola Barang</span>
            <svg class="sb-caret" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
          </summary>
          <div class="sb-submenu">
            <a href="{{ url('/tambah') }}" class="{{ request()->is('tambah') ? 'active' : '' }}">
              <span class="sb-dotmini"></span> Tambah Barang
            </a>
            <a href="{{ url('/edit') }}" class="{{ request()->is('edit') ? 'active' : '' }}">
              <span class="sb-dotmini"></span> Edit Barang
            </a>
            <a href="{{ url('/hapus') }}" class="{{ request()->is('hapus') ? 'active' : '' }}">
              <span class="sb-dotmini"></span> Hapus Barang
            </a>
          </div>
        </details>
      </li>

      <li>
        <details class="sb-acc" {{ $openLaporan ? 'open' : '' }}>
          <summary>
            <span class="sb-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
            </span>
            <span class="sb-label">Laporan Barang</span>
            <svg class="sb-caret" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
          </summary>
          <div class="sb-submenu">
            <a href="{{ url('/laporan/stok') }}" class="{{ request()->is('laporan/stok') ? 'active' : '' }}">
              <span class="sb-dotmini"></span> Stok
            </a>
            <a href="{{ url('/laporan/kadaluwarsa') }}" class="{{ request()->is('laporan/kadaluwarsa') ? 'active' : '' }}">
              <span class="sb-dotmini"></span> Kadaluwarsa
            </a>
          </div>
        </details>
      </li>

      <li>
        <a href="{{ url('/laporan/penjualan') }}" class="sb-pill {{ request()->is('laporan/penjualan') ? 'active' : '' }}">
          <span class="sb-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h2v18H3V3Zm16 0h2v18h-2V3ZM9 7h6v2H9V7Zm0 4h8v2H9v-2Zm0 4h5v2H9v-2Z"/></svg>
          </span>
          <span class="sb-label">Laporan Penjualan</span>
        </a>
      </li>
    </ul>

    {{-- CATATAN: Kartu "admin" di bawah sidebar sengaja DIHAPUS --}}
  </aside>

  <!-- MAIN CONTENT -->
  <main class="sb-content">
    {{-- Jika kamu pakai header dinamis --}}
    @php $isDashboard = $isDashboard ?? request()->is('dashboard'); @endphp
    @if($isDashboard && isset($header))
      <div class="sb-card mb-3 text-xl font-semibold">{{ $header }}</div>
    @endif

    {{-- Tempatkan konten halaman --}}
    {{ $slot ?? '' }}
    @yield('content')
  </main>
</div>
</body>
</html>
