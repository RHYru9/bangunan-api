<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Barang;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin,karyawan')->except(['listBarang']);
    }

    public function tambahbarang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:100',
            'gambar_barang' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'harga_barang' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'berat_barang' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validated = $validator->validated();

        // Handle image upload with custom path
        if ($request->hasFile('gambar_barang')) {
            $image = $request->file('gambar_barang');
            $extension = $image->getClientOriginalExtension();
            $fileName = Str::slug($validated['nama_barang']) . '_' . Str::random(8) . '.' . $extension;
            $path = $image->storeAs('img_barang', $fileName, 'public');
            $validated['gambar_barang'] = $path;
        }

        try {
            $barang = Barang::create($validated);

            return response()->json([
                'message' => 'Barang created successfully',
                'data' => $barang
            ], 201);
        } catch (\Exception $e) {
            // Delete uploaded image if creation fails
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'message' => 'Failed to create barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editbarang(Request $request, $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_barang' => 'sometimes|string|max:100',
            'gambar_barang' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'harga_barang' => 'sometimes|numeric|min:0',
            'stok' => 'sometimes|integer|min:0',
            'berat_barang' => 'sometimes|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validated = $validator->validated();
        $oldImagePath = null;

        // Handle image upload with custom path if new image is provided
        if ($request->hasFile('gambar_barang')) {
            // Store old image path for deletion if update succeeds
            $oldImagePath = $barang->gambar_barang;

            $image = $request->file('gambar_barang');
            $extension = $image->getClientOriginalExtension();
            $fileName = Str::slug($validated['nama_barang'] ?? $barang->nama_barang) . '_' . Str::random(8) . '.' . $extension;
            $path = $image->storeAs('img_barang', $fileName, 'public');
            $validated['gambar_barang'] = $path;
        }

        try {
            $barang->update($validated);

            // Delete old image if update was successful and new image was uploaded
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            return response()->json([
                'message' => 'Barang updated successfully',
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            // Delete new uploaded image if update fails
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'message' => 'Failed to update barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listBarang()
    {
        return response()->json([
            'data' => Barang::all()
        ]);
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
        if ($barang->gambar_barang && Storage::disk('public')->exists($barang->gambar_barang)) {
            Storage::disk('public')->delete($barang->gambar_barang);
        }

        $barang->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus'
        ], 200);
    }
}
