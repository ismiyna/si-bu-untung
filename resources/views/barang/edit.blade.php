<x-app-layout>
  <x-slot name="header"><span class="font-extrabold text-xl">Owner</span></x-slot>

  @php
    // menjaga state ketika kembali dari validasi
    $q          = old('q', $q ?? request('q'));
    $selectedId = old('id_barang', $selectedId ?? request('id'));
    $lastType   = old('type'); // legacy — tidak dipakai lagi setelah 1 tombol
  @endphp

  <style>
    .h1-title{font-weight:800;font-size:32px;line-height:1.2;text-align:center;margin:0 0 18px}
    .search-wrap{display:flex;justify-content:center;margin-bottom:22px}
    .search{position:relative;width:100%;max-width:560px}
    .search input{width:100%;height:40px;border:1px solid #e5e7eb;border-radius:999px;padding:0 14px 0 36px;background:#fff;font:500 14px/40px 'Poppins',system-ui}
    .search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af}

    .grid-2{display:grid;grid-template-columns:1fr;gap:18px;margin-bottom:22px}
    @media(min-width:860px){.grid-2{grid-template-columns:1fr 1fr}}

    .mini{background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 10px 25px rgba(0,0,0,.06);padding:14px}
    .mini .label{font:600 13px/1.4 'Poppins',system-ui;color:#374151;margin-bottom:8px}
    .mini .row{display:flex;gap:10px}
    .mini input[type="text"], .mini input[type="date"], .mini input[type="number"], .mini select{flex:1;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font:500 13px/20px 'Poppins',system-ui}
    .btn-ghost{border:0;background:#f3f4f6;border-radius:10px;padding:10px 14px;font:600 12px 'Poppins',system-ui;color:#111;cursor:pointer}
    .btn-ghost[disabled]{opacity:.6;cursor:not-allowed}
    .hint{display:flex;align-items:center;gap:6px;margin-top:8px;font:500 11px/1.4 'Poppins',system-ui;color:#9ca3af}
    .dot{width:8px;height:8px;border-radius:50%;background:#d1d5db;display:inline-block}

    .alert{margin:10px 0 18px;padding:10px 12px;border-radius:10px;font:600 13px 'Poppins',system-ui}
    .alert-success{background:#ecfdf5;color:#047857;border:1px solid #a7f3d0}
    .alert-error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}

    .tbl-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 10px 25px rgba(0,0,0,.06)}
    .tbl-title{padding:16px 20px;font:600 14px 'Poppins',system-ui}
    table{width:100%;border-collapse:collapse;font:500 13px 'Poppins',system-ui}
    thead th{background:#f3f4f6;text-align:left;padding:10px 14px;border-bottom:1px solid #e5e7eb}
    tbody td{padding:10px 14px;border-top:1px solid #f0f0f0}
    tbody tr:nth-child(odd){background:#fcfcfc}

    .pagination{display:flex;justify-content:center;gap:6px;padding:12px 16px}
    .page-btn{min-width:28px;height:28px;padding:0 8px;border:1px solid #e5e7eb;background:#fff;border-radius:6px;font:600 12px/28px 'Poppins',system-ui;text-align:center;color:#111}
    .page-btn.is-active{background:#2563eb;color:#fff;border-color:#2563eb}
    .page-btn.icon{font-weight:600}
    .page-container{max-width:1100px;margin:0 auto}
    .err{margin-top:8px;color:#b91c1c;font:600 12px 'Poppins',system-ui}

    .actions{display:flex;justify-content:flex-end;margin-top:8px}
    .btn-primary{background:#2563eb;color:#fff;border:none;border-radius:10px;padding:12px 18px;font:700 13px 'Poppins',system-ui;cursor:pointer}
    .btn-primary[disabled]{opacity:.6;cursor:not-allowed}
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
      <form class="search" method="get" action="{{ route('ui.edit') }}">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M15.5 14h-.79l-.28-.27A6.5 6.5 0 1 0 14 15.5l.27.28h.79l5 5 1.5-1.5-5-5ZM10 15.5A5.5 5.5 0 1 1 10 4.5a5.5 5.5 0 0 1 0 11Z"/>
        </svg>
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari Barang...">
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

        const updateAction = (id) => {
          // route('barang.update','__ID__') diganti dinamis
          form.action = "{{ route('barang.update', ['barang' => '__ID__']) }}".replace('__ID__', encodeURIComponent(id));
        };

        btnPick.addEventListener('click', async function () {
          const p = (prefix.value || '').toLowerCase();
          let n = (numIn.value || '').replace(/\D/g,'');
          if (!p || n.length === 0) {
            alert('Pilih prefix dan isi nomor (3 digit).');
            return;
          }
          n = n.padStart(3,'0');
          const id = p + n;

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
              return;
            }
            const b = await res.json();

            // isi form
            fId.value     = b.id_barang || id;
            fNama.value   = b.nama_barang ?? '';
            fStok.value   = b.stok_barang ?? 0;
            fHarga.value  = b.harga_satuan ?? 0;
            fGambar.value = b.gambar_url ?? '';
            fTgl.value    = b.tanggal_kedaluwarsa ?? '';

            updateAction(b.id_barang || id);
            btnSave.disabled = false;
          } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan.');
          }
        });

        // Guard saat submit
        form.addEventListener('submit', function (ev) {
          if (!fId.value) {
            ev.preventDefault();
            alert('Pilih ID barang terlebih dahulu.');
          }
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
