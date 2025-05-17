<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['masuk', 'daftar', 'refresh']]);
        $this->middleware('role:admin')->only([
            'listUsersByRole', 'hapus', 'hapusByRole', 'daftarAdmin'
        ]);
    }

    // User Registration
    public function daftar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:40|min:5',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user'
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    // Login
    public function masuk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    // Admin create admin
    public function daftarAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:40|min:5',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin'
        ]);

        return response()->json([
            'message' => 'Admin created successfully',
            'user' => $user
        ], 201);
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

//LIST Pengguna
    public function listUsersByRole(Request $request)
    {
    $role = $request->query('role', 'all');

    $allowedRoles = ['admin', 'user', 'karyawan', 'all'];

    if (!in_array($role, $allowedRoles)) {
        return response()->json(['message' => 'Role tidak valid'], 400);
    }

    if ($role === 'all') {
        // Ambil semua user tanpa filter role
        $users = User::all();
        $messageRole = 'semua pengguna';
    } else {
        // Ambil user sesuai role
        $users = User::where('role', $role)->get();
        $messageRole = $role;
    }

    return response()->json([
        'message' => "Daftar {$messageRole}",
        'data' => $users
    ], 200);
    }

    /**
     * Hapus user by role dan id
     * Only admin can do this
     */
    public function hapusByRole($role, $id)
    {
        $allowedRoles = ['admin', 'user', 'karyawan'];

        if (!in_array($role, $allowedRoles)) {
            return response()->json(['message' => 'Role tidak valid'], 400);
        }

        $user = User::where('role', $role)->find($id);

        if (!$user) {
            return response()->json(['message' => ucfirst($role) . ' tidak ditemukan'], 404);
        }

        // Jika admin, jangan bisa hapus diri sendiri
        if ($role === 'admin' && $user->id === auth()->user()->id) {
            return response()->json(['error' => 'Tidak bisa menghapus akun sendiri'], 400);
        }

        $user->delete();

        return response()->json(['message' => ucfirst($role) . ' berhasil dihapus'], 200);
    }

    // User saat ini
    public function saya()
    {
        return response()->json(auth()->user());
    }

    // Logout
    public function keluar()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout berhasil']);
    }

    // Refresh token
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    // Helper token response
    protected function respondWithToken($token)
    {
        $user = auth()->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
                'alamat' => $user->alamat,
                'kode_pos' => $user->kode_pos
            ]
        ]);
    }
}
