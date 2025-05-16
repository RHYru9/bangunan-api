<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['masuk', 'daftar', 'saya', 'keluar']]);
    }

    public function daftar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:40|min:5',
            'email' => 'required|email|unique:users,email',
            'alamat' => 'nullable|string',
            'kode_pos' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User berhasil dibuat',
            'user' => $user
        ], 201);
    }

    public function masuk(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function listuser(Request $request)
    {
        $users = User::all();

        return response()->json([
            'message' => 'Daftar users',
            'data' => $users
        ], 200);
    }

    //hapus user
    public function hapus($id)
    {
    $users = User::find($id);

    if (!$users) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $users->delete();

    return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
