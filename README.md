# Dokumentasi API (Format Burp Suite) -> ongoing

Berikut adalah dokumentasi API Toko Bangunan:

## Autentikasi Pengguna

### Pendaftaran Pengguna
```
POST /api/auth/daftar HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
  "nama": "Nama Lengkap",
  "email": "user@example.com",
  "alamat": "Jl. Contoh No. 1",
  "kode_pos": "12345",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login Pengguna
```
POST /api/auth/masuk HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

### Daftar Pengguna
```
GET /api/auth/users HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer {access_token}
Accept: application/json
```

### Hapus Pengguna
```
DELETE /api/auth/user/123 HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer {access_token}
Accept: application/json
```

## Autentikasi Admin

### Pendaftaran Admin
```
POST /api/auth/admin/daftar HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json
Authorization: Bearer {access_token}

{
  "nama": "Admin Baru",
  "email": "admin@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login Admin
```
POST /api/auth/admin/login HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
  "email": "admin@example.com",
  "password": "password123"
}
```

### Daftar Admin
```
GET /api/auth/admins HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer {access_token}
Accept: application/json
```

### Hapus Admin
```
DELETE /api/auth/admin/delete/123 HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer {access_token}
Accept: application/json
```

## Manajemen Produk

### Daftar Barang
```
GET /api/produk/barang-list HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer {access_token}
Accept: application/json
```

### Tambah Barang
```
POST /api/produk/barang-tambah HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer {access_token}
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW
Accept: application/json

------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="nama_barang"

Produk Contoh
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="gambar_barang"; filename="produk.jpg"
Content-Type: image/jpeg

(binary data)
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="harga_barang"

150000
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="berat_barang"

2.5
------WebKitFormBoundary7MA4YWxkTrZu0gW--
```

### Hapus Barang
```
DELETE /api/produk/barang-hapus/123 HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer {access_token}
Accept: application/json
```


### Autentikasi Pengguna
- `POST /api/auth/daftar` - Mendaftarkan pengguna baru
- `POST /api/auth/masuk` - Login pengguna
- `GET /api/auth/users` - Mendapatkan daftar pengguna (memerlukan auth)
- `DELETE /api/auth/user/{id}` - Menghapus pengguna (memerlukan auth)

### Autentikasi Admin
- `POST /api/auth/admin/daftar` - Mendaftarkan admin baru (memerlukan auth)
- `POST /api/auth/admin/login` - Login admin
- `GET /api/auth/admins` - Mendapatkan daftar admin (memerlukan auth)
- `DELETE /api/auth/admin/delete/{id}` - Menghapus admin (memerlukan auth)

### Manajemen Produk
- `GET /api/produk/barang-list` - Mendapatkan daftar barang (memerlukan auth)
- `POST /api/produk/barang-tambah` - Menambahkan barang baru (memerlukan auth)
- `DELETE /api/produk/barang-hapus/{id}` - Menghapus barang (memerlukan auth)

## Cara Penggunaan

1. Untuk endpoint yang memerlukan autentikasi, tambahkan header:
   ```
   Authorization: Bearer {access_token}
   ```
2. Untuk upload gambar, gunakan content-type `multipart/form-data`
3. Untuk request body, gunakan format JSON kecuali untuk upload file
