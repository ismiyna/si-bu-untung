<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    // ====== RESOURCE CRUD ======

    public function index(Request $request)
    {
        $q = $request->query('q');

        $barangs = Barang::when($q, function ($qb) use ($q) {
                $qb->where('id_barang', 'like', "%{$q}%")
                   ->orWhere('nama_barang', 'like', "%{$q}%");
            })
            ->orderBy('nama_barang')
            ->paginate(10)
            ->withQueryString();

        return view('barang.index', compact('barangs', 'q'));
    }

    public function create()
    {
        return view('barang.form', ['barang' => new Barang()]);
    }

    public function store(Request $request)
    {
        // Alur form lengkap (resource)
        $data = $request->validate([
            'id_barang'            => ['required','string','max:20','unique:barang,id_barang'],
            'nama_barang'          => ['required','string'],
            'stok_barang'          => ['required','integer','min:0'],
            'harga_satuan'         => ['required','numeric','min:0'],
            'gambar_url'           => ['nullable','string'],
            'tanggal_kedaluwarsa'  => ['nullable','date'],
        ]);

        Barang::create($data);
        return redirect()->route('barang.index')->with('success','Barang ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('barang.form', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'id_barang'            => ['required','string','max:20', Rule::unique('barang','id_barang')->ignore($barang->id_barang, 'id_barang')],
            'nama_barang'          => ['required','string'],
            'stok_barang'          => ['required','integer','min:0'],
            'harga_satuan'         => ['required','numeric','min:0'],
            'gambar_url'           => ['nullable','string'],
            'tanggal_kedaluwarsa'  => ['nullable','date'],
        ]);

        $barang->update($data);

        // === Tetap di halaman /edit setelah simpan ===
        $to = $request->input('redirect_to');
        if (!empty($to)) {
            return redirect($to)->with('success','Barang diperbarui.');
        }
        // fallback aman bila hidden tidak ikut terkirim
        return back()->with('success','Barang diperbarui.');
    }

    public function destroy(Request $request, Barang $barang)
    {
        $barang->delete();

        $to = $request->input('redirect_to') ?: route('ui.hapus');
        return redirect($to)->with('success','Barang dihapus.');
    }

    // ====== QUICK PAGES (mockup sidebar) ======

    /** UI Edit (search + kartu mini + tabel) */
    public function quickEditPage(Request $request)
    {
        $q          = $request->query('q');
        $selectedId = $request->query('id');

        $barangs = Barang::when($q, function ($qb) use ($q) {
                $qb->where('id_barang', 'like', "%{$q}%")
                   ->orWhere('nama_barang', 'like', "%{$q}%");
            })
            ->orderBy('id_barang')
            ->paginate(8)
            ->withQueryString();

        return view('barang.edit', compact('barangs', 'q', 'selectedId'));
    }

    /** Quick insert dari halaman /tambah (form cepat/minimal) */
    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'id_barang'            => ['required','string','max:20','unique:barang,id_barang'],
            'nama_barang'          => ['required','string'],
            'stok_barang'          => ['nullable','integer','min:0'],
            'harga_satuan'         => ['nullable','numeric','min:0'],
            'gambar_url'           => ['nullable','string'],
            'tanggal_kedaluwarsa'  => ['nullable','date'],
        ]);

        $data['stok_barang']  = $data['stok_barang']  ?? 0;
        $data['harga_satuan'] = $data['harga_satuan'] ?? 0;

        Barang::create($data);

        return redirect()->route('ui.tambah')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Quick update satu field dari kartu mini (edit-page).
     * Form mengirim: id_barang, type (nama|tanggal|stok|harga|gambar), value
     */
    public function quickUpdate(Request $request)
    {
        $request->validate([
            'id_barang' => ['required','exists:barang,id_barang'],
            'type'      => ['required','in:nama,tanggal,stok,harga,gambar'],
        ]);

        $barang = Barang::where('id_barang', $request->id_barang)->firstOrFail();

        switch ($request->type) {
            case 'nama':
                $request->validate(['value' => ['required','string']]);
                $barang->nama_barang = $request->value;
                $barang->save();
                $msg = 'Nama barang diperbarui.';
                break;

            case 'tanggal':
                if ($request->filled('value')) {
                    $request->validate(['value' => ['date']]);
                    $barang->tanggal_kedaluwarsa = $request->value; // Y-m-d
                } else {
                    $barang->tanggal_kedaluwarsa = null;
                }
                $barang->save();
                $msg = 'Tanggal kedaluwarsa diperbarui.';
                break;

            case 'stok':
                $request->validate(['value' => ['required','integer','min:0']]);
                $barang->stok_barang = (int) $request->value;
                $barang->save();
                $msg = 'Stok barang diperbarui.';
                break;

            case 'harga':
                $request->validate(['value' => ['required','numeric','min:0']]);
                $barang->harga_satuan = (float) $request->value;
                $barang->save();
                $msg = 'Harga satuan diperbarui.';
                break;

            case 'gambar':
                $request->validate(['value' => ['nullable','string']]);
                $barang->gambar_url = $request->value;
                $barang->save();
                $msg = 'Gambar URL diperbarui.';
                break;

            default:
                $msg = 'Tidak ada perubahan.';
        }

        return back()
            ->withInput($request->only('q','id_barang','type','value'))
            ->with('success', $msg);
    }

    /**
     * ===== Generate next id_barang berdasarkan prefix (AJAX) =====
     * GET /barang/next-id?prefix=rkk -> { "next_id": "rkk001" }
     */
    public function nextId(Request $request)
    {
        $allowedPrefixes = [
            'rkk'  => 'Rokok',
            'mnyk' => 'Minyak',
            'brs'  => 'Beras',
            'mnm'  => 'Minuman',
        ];

        $prefix = strtolower(trim($request->query('prefix', '')));

        if (! array_key_exists($prefix, $allowedPrefixes)) {
            return response()->json(['message' => 'Prefix tidak dikenali.'], 422);
        }

        $maxId = Barang::where('id_barang', 'like', $prefix.'%')->max('id_barang');

        $nextNum = 1;
        if ($maxId && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $maxId, $m)) {
            $nextNum = ((int) $m[1]) + 1;
        }

        $suffix = str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT);

        return response()->json(['next_id' => $prefix.$suffix]);
    }

    /**
     * ===== Lookup barang by id untuk AUTOFILL (AJAX) =====
     * GET /barang/find?id=rkk009
     */
    public function findById(Request $request)
    {
        $id = $request->query('id');

        if (! $id) {
            return response()->json(['message' => 'Parameter id wajib diisi.'], 422);
        }

        $barang = Barang::where('id_barang', $id)->first();

        if (! $barang) {
            return response()->json(['message' => 'Barang tidak ditemukan.'], 404);
        }

        return response()->json([
            'id_barang'           => $barang->id_barang,
            'nama_barang'         => $barang->nama_barang,
            'stok_barang'         => (int) $barang->stok_barang,
            'harga_satuan'        => (float) ($barang->harga_satuan ?? 0),
            'gambar_url'          => $barang->gambar_url,
            'tanggal_kedaluwarsa' => optional($barang->tanggal_kedaluwarsa)->format('Y-m-d'),
        ]);
    }
}
