<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['masukAdmin', 'daftarAdmin', 'refresh']]);
    }

    // Daftar admin baru
    public function daftarAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|min:6|max:20',
            'email' => 'required|email|max:70|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|integer', // 1 = admin, 2 = karyawan
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $admin = Admin::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 1,
        ]);

        return response()->json([
            'message' => 'Admin berhasil dibuat',
            'admin' => $admin
        ], 201);
    }

    // Login admin
    public function masukAdmin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('admin')->attempt($credentials)) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function listAdmin()
    {
        $admins = Admin::all();

        return response()->json([
            'message' => 'Daftar admin',
            'data' => $admins
        ], 200);
    }

    public function hapusAdmin($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $admin->delete();

        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('admin')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60
        ]);
    }
}
