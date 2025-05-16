<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;  // import Str untuk random string
use App\Models\Barang;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['refresh', 'listBarang']]);
    }

    public function tambahbarang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang'   => 'required|string|max:40|min:1',
            'gambar_barang' => 'required|file|mimes:jpg,jpeg,png,webp,svg|max:2048', // max 2MB, include svg
            'harga_barang'  => 'required|numeric|min:0',
            'berat_barang'  => 'required|numeric|max:1000|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        if ($request->hasFile('gambar_barang')) {
            $file = $request->file('gambar_barang');
            // nama_barang jadi lowercase, spasi diganti underscore
            $prefix = strtolower(str_replace(' ', '_', $request->input('nama_barang')));
            $randomString = Str::random(20);
            $filename = $prefix . '_' . $randomString . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('gambar_barang', $filename, 'public');
            $validated['gambar_barang'] = $path; // simpan path relatif di DB
        }

        $barang = Barang::create($validated);

        return response()->json([
            'message' => 'Barang berhasil ditambahkan',
            'data' => $barang
        ], 201);
    }

    public function listBarang()
    {
        $barangs = Barang::all();

        if ($barangs->isEmpty()) {
            return response()->json([
                'message' => 'Data barang kosong',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Daftar barang berhasil diambil',
            'data' => $barangs
        ], 200);
    }

    public function hapusbarang($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        // Hapus file gambar juga jika ada
        if ($barang->gambar_barang && \Storage::disk('public')->exists($barang->gambar_barang)) {
            \Storage::disk('public')->delete($barang->gambar_barang);
        }

        $barang->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus'
        ], 200);
    }
}
