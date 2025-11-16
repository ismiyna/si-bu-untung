<x-app-layout>
  <x-slot name="header"><span class="font-extrabold text-xl">Owner</span></x-slot>

  @php
    // menjaga state ketika kembali dari validasi
    $q          = old('q', $q ?? request('q'));
    $selectedId = old('id_barang', $selectedId ?? request('id'));
    $lastType   = old('type'); // legacy — tidak dipakai lagi setelah 1 tombol
  @endphp

  <style>
    /* Keyframes Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    @keyframes shimmer {
      0% { background-position: -1000px 0; }
      100% { background-position: 1000px 0; }
    }

    /* Base Styles */
    .h1-title{
      font-weight:800;font-size:32px;line-height:1.2;text-align:center;margin:0 0 18px;
      animation: fadeInUp 0.6s ease-out;
    }
    .search-wrap{
      display:flex;justify-content:center;margin-bottom:22px;
      animation: fadeInUp 0.6s ease-out 0.1s both;
    }
    .search{
      position:relative;width:100%;max-width:560px;
      transition: transform 0.3s ease;
    }
    .search:hover {
      transform: scale(1.02);
    }
    .search input{
      width:100%;height:40px;border:1px solid #e5e7eb;border-radius:999px;
      padding:0 14px 0 36px;background:#fff;font:500 14px/40px 'Poppins',system-ui;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .search input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      transform: scale(1.02);
    }
    .search-icon{
      position:absolute;left:12px;top:50%;transform:translateY(-50%);
      width:18px;height:18px;cursor:pointer;
      transition: all 0.3s ease;
      z-index: 10;
      pointer-events: auto;
      background: none;
      border: none;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .search-icon img {
      width: 100%;
      height: 100%;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      filter: brightness(0) saturate(100%) invert(60%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(90%) contrast(90%);
      display: block;
    }
    .search-icon:hover {
      transform: translateY(-50%) scale(1.15);
    }
    .search-icon:hover img {
      filter: brightness(0) saturate(100%) invert(37%) sepia(96%) saturate(7498%) hue-rotate(214deg) brightness(99%) contrast(101%);
      transform: scale(1.1);
    }
    .search-icon:active {
      transform: translateY(-50%) scale(1.05);
    }
    .search:focus-within .search-icon {
      transform: translateY(-50%) scale(1.1);
    }
    .search:focus-within .search-icon img {
      filter: brightness(0) saturate(100%) invert(37%) sepia(96%) saturate(7498%) hue-rotate(214deg) brightness(99%) contrast(101%);
    }

    .grid-2{
      display:grid;grid-template-columns:1fr;gap:18px;margin-bottom:22px;
    }
    @media(min-width:860px){.grid-2{grid-template-columns:1fr 1fr}}

    .mini{
      background:#fff;border:1px solid #e5e7eb;border-radius:14px;
      box-shadow:0 10px 25px rgba(0,0,0,.06);padding:14px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: fadeInUp 0.5s ease-out both;
      position: relative;
      overflow: hidden;
    }
    .mini::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s;
    }
    .mini:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(0,0,0,.1);
      border-color: #2563eb;
    }
    .mini:hover::before {
      left: 100%;
    }
    .mini .label{
      font:600 13px/1.4 'Poppins',system-ui;color:#374151;margin-bottom:8px;
      transition: color 0.3s ease;
    }
    .mini:hover .label {
      color: #2563eb;
    }
    .mini .row{display:flex;gap:10px}
    .mini input[type="text"], 
    .mini input[type="date"], 
    .mini input[type="number"], 
    .mini select{
      flex:1;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;
      font:500 13px/20px 'Poppins',system-ui;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      background: #fff;
    }
    .mini input:focus, .mini select:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      transform: translateY(-2px);
    }
    .btn-ghost{
      border:0;background:#f3f4f6;border-radius:10px;padding:10px 14px;
      font:600 12px 'Poppins',system-ui;color:#111;cursor:pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    .btn-ghost::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(37, 99, 235, 0.1);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    .btn-ghost:hover::before {
      width: 300px;
      height: 300px;
    }
    .btn-ghost:hover {
      background: #e0e7ff;
      color: #2563eb;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .btn-ghost:active {
      transform: translateY(0) scale(0.98);
    }
    .btn-ghost[disabled]{
      opacity:.6;cursor:not-allowed;
      transform: none !important;
    }
    .btn-ghost.loading {
      pointer-events: none;
      position: relative;
      color: transparent;
    }
    .btn-ghost.loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 16px;
      height: 16px;
      margin: -8px 0 0 -8px;
      border: 2px solid #2563eb;
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }
    .hint{
      display:flex;align-items:center;gap:6px;margin-top:8px;
      font:500 11px/1.4 'Poppins',system-ui;color:#9ca3af;
      transition: color 0.3s ease;
    }
    .mini:hover .hint {
      color: #6b7280;
    }
    .dot{
      width:8px;height:8px;border-radius:50%;background:#d1d5db;
      display:inline-block;
      animation: pulse 2s ease-in-out infinite;
    }

    .alert{
      margin:10px 0 18px;padding:10px 12px;border-radius:10px;
      font:600 13px 'Poppins',system-ui;
      animation: slideInRight 0.5s ease-out, fadeIn 0.5s ease-out;
      transform-origin: left center;
    }
    .alert-success{
      background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;
      box-shadow: 0 4px 12px rgba(4, 120, 87, 0.1);
    }
    .alert-error{
      background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;
      box-shadow: 0 4px 12px rgba(185, 28, 28, 0.1);
      animation: slideInRight 0.5s ease-out, fadeIn 0.5s ease-out, shake 0.5s ease-out 0.5s;
    }

    .tbl-card{
      background:#fff;border:1px solid #e5e7eb;border-radius:14px;
      box-shadow:0 10px 25px rgba(0,0,0,.06);
      animation: fadeInUp 0.6s ease-out 0.2s both;
      transition: box-shadow 0.3s ease;
    }
    .tbl-card:hover {
      box-shadow: 0 15px 35px rgba(0,0,0,.1);
    }
    .tbl-title{
      padding:16px 20px;font:600 14px 'Poppins',system-ui;
      border-bottom: 1px solid #f3f4f6;
    }
    table{
      width:100%;border-collapse:collapse;font:500 13px 'Poppins',system-ui;
    }
    thead th{
      background:#f3f4f6;text-align:left;padding:10px 14px;
      border-bottom:1px solid #e5e7eb;
      transition: background 0.3s ease;
    }
    tbody td{
      padding:10px 14px;border-top:1px solid #f0f0f0;
      transition: all 0.2s ease;
    }
    tbody tr{
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: fadeIn 0.4s ease-out both;
    }
    tbody tr:nth-child(odd){background:#fcfcfc}
    tbody tr:hover {
      background: #eff6ff !important;
      transform: scale(1.01);
      box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
    }
    tbody tr:hover td {
      color: #2563eb;
    }
    tbody tr:nth-child(1) { animation-delay: 0.05s; }
    tbody tr:nth-child(2) { animation-delay: 0.1s; }
    tbody tr:nth-child(3) { animation-delay: 0.15s; }
    tbody tr:nth-child(4) { animation-delay: 0.2s; }
    tbody tr:nth-child(5) { animation-delay: 0.25s; }

    .pagination{
      display:flex;justify-content:center;gap:6px;padding:12px 16px;
    }
    .page-btn{
      min-width:28px;height:28px;padding:0 8px;border:1px solid #e5e7eb;
      background:#fff;border-radius:6px;font:600 12px/28px 'Poppins',system-ui;
      text-align:center;color:#111;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      display: inline-block;
      position: relative;
      overflow: hidden;
    }
    .page-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(37, 99, 235, 0.1);
      transform: translate(-50%, -50%);
      transition: width 0.4s, height 0.4s;
    }
    .page-btn:hover::before {
      width: 100px;
      height: 100px;
    }
    .page-btn:hover {
      border-color: #2563eb;
      color: #2563eb;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .page-btn.is-active{
      background:#2563eb;color:#fff;border-color:#2563eb;
      animation: pulse 2s ease-in-out infinite;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .page-btn.icon{font-weight:600}
    .page-container{
      max-width:1100px;margin:0 auto;
      animation: fadeIn 0.4s ease-out;
    }
    .err{
      margin-top:8px;color:#b91c1c;font:600 12px 'Poppins',system-ui;
      animation: shake 0.5s ease-out;
    }

    .actions{
      display:flex;justify-content:flex-end;margin-top:8px;
      animation: fadeInUp 0.6s ease-out 0.3s both;
    }
    .btn-primary{
      background:#2563eb;color:#fff;border:none;border-radius:10px;
      padding:12px 18px;font:700 13px 'Poppins',system-ui;cursor:pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .btn-primary::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    .btn-primary:hover::before {
      width: 400px;
      height: 400px;
    }
    .btn-primary:hover {
      background: #1d4ed8;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    }
    .btn-primary:active {
      transform: translateY(0) scale(0.98);
    }
    .btn-primary[disabled]{
      opacity:.6;cursor:not-allowed;
      transform: none !important;
      box-shadow: none;
    }
    .btn-primary.loading {
      pointer-events: none;
      position: relative;
      color: transparent;
    }
    .btn-primary.loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      margin: -10px 0 0 -10px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    /* Stagger animation for grid items */
    .grid-2 .mini:nth-child(1) { animation-delay: 0.1s; }
    .grid-2 .mini:nth-child(2) { animation-delay: 0.15s; }
    .grid-2 .mini:nth-child(3) { animation-delay: 0.2s; }
    .grid-2 .mini:nth-child(4) { animation-delay: 0.25s; }
    .grid-2 .mini:nth-child(5) { animation-delay: 0.3s; }
    .grid-2 .mini:nth-child(6) { animation-delay: 0.35s; }

    /* Image hover effect */
    table img {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    tbody tr:hover img {
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
  </style>

  <div class="page-container">
    <h1 class="h1-title">Cari barang yang ingin di edit</h1>

    {{-- Flash --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any() && !$lastType)
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    {{-- Search --}}
    <div class="search-wrap">
      <form class="search" method="get" action="{{ route('ui.edit') }}" id="searchForm">
        <button type="submit" class="search-icon" aria-label="Cari">
          <img src="{{ asset('assets/icon-search.svg') }}" alt="Search Icon">
        </button>
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari Barang..." id="searchInput">
        @if(!empty($selectedId))
          <input type="hidden" name="id" value="{{ $selectedId }}">
        @endif
      </form>
    </div>

    {{-- ===================== PICKER ID + FORM UTAMA ===================== --}}
    <div class="grid-2">
      {{-- Picker ID: Prefix + Nomor + Pilih --}}
      <div class="mini">
        <div class="label">Id Barang</div>
        <div class="row">
          <select id="prefixSelect" aria-label="Prefix">
            <option value="">Pilih Id Barang</option>
            <option value="rkk">RKK (Rokok)</option>
            <option value="mnyk">MNYK (Minyak)</option>
            <option value="brs">BRS (Beras)</option>
            <option value="mnm">MNM (Minuman)</option>
          </select>
          <input type="text" id="numInput" placeholder="Nomor (cth: 009)" inputmode="numeric" pattern="[0-9]*" maxlength="3">
          <button type="button" id="btnPick" class="btn-ghost">Pilih</button>
        </div>
        <div class="hint"><span class="dot"></span>Tulis nomor lalu klik <b>Pilih</b> <span style="opacity:.7">(contoh: RKK + 009 → rkk009)</span></div>
      </div>

      {{-- Tanggal Kadaluwarsa (bagian form utama) --}}
      <div class="mini">
        <div class="label">Tanggal Kadaluwarsa</div>
        <input form="bulkForm" type="date" name="tanggal_kedaluwarsa" id="f_tanggal">
        <div class="hint"><span class="dot"></span>Pilih Tanggal Kadaluwarsa yang ingin di edit</div>
      </div>

      {{-- Nama --}}
      <div class="mini">
        <div class="label">Nama Barang</div>
        <input form="bulkForm" type="text" name="nama_barang" id="f_nama" placeholder="Masukan Nama Barang">
        <div class="hint"><span class="dot"></span>Masukan Nama Barang yang ingin di edit</div>
      </div>

      {{-- Stok --}}
      <div class="mini">
        <div class="label">Stok Barang</div>
        <input form="bulkForm" type="number" min="0" name="stok_barang" id="f_stok" placeholder="Masukan Stok Barang">
        <div class="hint"><span class="dot"></span>Masukan Stok Barang yang ingin di edit</div>
      </div>

      {{-- Harga --}}
      <div class="mini">
        <div class="label">Harga Satuan (Rp)</div>
        <input form="bulkForm" type="number" min="0" name="harga_satuan" id="f_harga" placeholder="Masukan Harga Satuan">
        <div class="hint"><span class="dot"></span>Masukan harga satuan (dalam rupiah)</div>
      </div>

      {{-- Gambar --}}
      <div class="mini">
        <div class="label">Gambar URL (link)</div>
        <input form="bulkForm" type="text" name="gambar_url" id="f_gambar" placeholder="https://contoh.com/gambar.jpg">
        <div class="hint"><span class="dot"></span>Tempelkan link gambar (opsional)</div>
      </div>
    </div>

    {{-- FORM UTAMA (satu tombol) --}}
    <form id="bulkForm" method="post">
      @csrf
      @method('PATCH')
      {{-- id_barang ikut dikirim; tampil readonly agar tidak salah ubah --}}
      <input type="hidden" name="id_barang" id="f_id">
      <div class="actions">
        <button id="btnSave" class="btn-primary" type="submit" disabled>Simpan Perubahan</button>
      </div>
    </form>

    {{-- JS: Picker → fetch barang.find → isi form; submit → route update/{id} --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Search form functionality
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        
        // Enter key untuk submit search
        if (searchInput) {
          searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
              e.preventDefault();
              searchForm.submit();
            }
          });
        }

        const prefix  = document.getElementById('prefixSelect');
        const numIn   = document.getElementById('numInput');
        const btnPick = document.getElementById('btnPick');

        const form    = document.getElementById('bulkForm');
        const btnSave = document.getElementById('btnSave');

        const fId     = document.getElementById('f_id');
        const fNama   = document.getElementById('f_nama');
        const fStok   = document.getElementById('f_stok');
        const fHarga  = document.getElementById('f_harga');
        const fGambar = document.getElementById('f_gambar');
        const fTgl    = document.getElementById('f_tanggal');

        // Helper untuk animasi input fields
        const animateInputFill = (input, value) => {
          if (!input) return;
          input.style.opacity = '0';
          input.style.transform = 'translateY(-10px)';
          setTimeout(() => {
            input.value = value;
            input.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            input.style.opacity = '1';
            input.style.transform = 'translateY(0)';
            // Highlight effect
            input.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.2)';
            setTimeout(() => {
              input.style.boxShadow = '';
            }, 600);
          }, 100);
        };

        const updateAction = (id) => {
          // route('barang.update','__ID__') diganti dinamis
          form.action = "{{ route('barang.update', ['barang' => '__ID__']) }}".replace('__ID__', encodeURIComponent(id));
        };

        btnPick.addEventListener('click', async function () {
          const p = (prefix.value || '').toLowerCase();
          let n = (numIn.value || '').replace(/\D/g,'');
          if (!p || n.length === 0) {
            // Shake animation untuk error
            btnPick.style.animation = 'shake 0.5s ease-out';
            setTimeout(() => {
              btnPick.style.animation = '';
            }, 500);
            alert('Pilih prefix dan isi nomor (3 digit).');
            return;
          }
          n = n.padStart(3,'0');
          const id = p + n;

          // Loading state
          btnPick.classList.add('loading');
          btnPick.disabled = true;

          try {
            const res = await fetch("{{ route('barang.find') }}?id=" + encodeURIComponent(id), {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) {
              if (res.status === 404) {
                alert('Barang dengan ID ' + id + ' tidak ditemukan.');
              } else {
                alert('Gagal mengambil data barang.');
              }
              btnPick.classList.remove('loading');
              btnPick.disabled = false;
              return;
            }
            const b = await res.json();

            // Animate form fill dengan delay untuk efek cascade
            animateInputFill(fId, b.id_barang || id);
            setTimeout(() => animateInputFill(fNama, b.nama_barang ?? ''), 100);
            setTimeout(() => animateInputFill(fStok, b.stok_barang ?? 0), 200);
            setTimeout(() => animateInputFill(fHarga, b.harga_satuan ?? 0), 300);
            setTimeout(() => animateInputFill(fGambar, b.gambar_url ?? ''), 400);
            setTimeout(() => animateInputFill(fTgl, b.tanggal_kedaluwarsa ?? ''), 500);

            updateAction(b.id_barang || id);
            
            // Enable save button dengan animasi
            setTimeout(() => {
              btnSave.disabled = false;
              btnSave.style.animation = 'pulse 0.6s ease-out';
              setTimeout(() => {
                btnSave.style.animation = '';
              }, 600);
            }, 600);

            btnPick.classList.remove('loading');
            btnPick.disabled = false;
          } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan.');
            btnPick.classList.remove('loading');
            btnPick.disabled = false;
          }
        });

        // Guard saat submit dengan loading state
        form.addEventListener('submit', function (ev) {
          if (!fId.value) {
            ev.preventDefault();
            btnSave.style.animation = 'shake 0.5s ease-out';
            setTimeout(() => {
              btnSave.style.animation = '';
            }, 500);
            alert('Pilih ID barang terlebih dahulu.');
            return;
          }
          
          // Loading state saat submit
          btnSave.classList.add('loading');
          btnSave.disabled = true;
          
          // Form akan submit secara normal, loading akan hilang setelah redirect
        });

        // Animasi saat input fields diisi manual
        [fNama, fStok, fHarga, fGambar, fTgl].forEach(input => {
          if (input) {
            input.addEventListener('input', function() {
              if (this.value && !btnSave.disabled) {
                this.style.borderColor = '#10b981';
                setTimeout(() => {
                  this.style.borderColor = '';
                }, 1000);
              }
            });
          }
        });

        // Smooth scroll untuk alert messages
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
          alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
      });
    </script>

    {{-- =================== Tabel + pagination =================== --}}
    <div class="tbl-card" style="margin-top:18px">
      <div class="tbl-title">Table Barang</div>
      <div style="overflow-x:auto">
        <table>
          <thead>
            <tr>
              <th>Id Barang</th>
              <th>Nama Barang</th>
              <th>Harga Satuan</th>
              <th>Gambar</th>
              <th>Tanggal Kadaluwarsa</th>
              <th>Stok Barang</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($barangs ?? []) as $b)
              <tr>
                <td>{{ $b->id_barang }}</td>
                <td>{{ $b->nama_barang }}</td>
                <td>
                  @php $h = (int) ($b->harga_satuan ?? 0); @endphp
                  {{ $h > 0 ? 'Rp '.number_format($h, 0, ',', '.') : '-' }}
                </td>
                <td>
                  @if(!empty($b->gambar_url))
                    <img src="{{ $b->gambar_url }}" alt="img" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #eee">
                  @else
                    -
                  @endif
                </td>
                <td>{{ optional($b->tanggal_kedaluwarsa)->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $b->stok_barang }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center;color:#6b7280;padding:16px">Tidak ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @isset($barangs)
        @php $current=$barangs->currentPage(); $last=$barangs->lastPage(); @endphp
        <div class="pagination">
          <a class="page-btn icon" href="{{ $barangs->previousPageUrl() ?? '#' }}">«</a>
          @for($i=max(1,$current-2); $i<=min($last,$current+2); $i++)
            <a class="page-btn {{ $i==$current?'is-active':'' }}" href="{{ $barangs->url($i) }}">{{ $i }}</a>
          @endfor
          <a class="page-btn icon" href="{{ $barangs->nextPageUrl() ?? '#' }}">»</a>
        </div>
      @endisset
    </div>
  </div>
</x-app-layout>
