<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'nama_barang', 'gambar_barang', 'berat_barang', 'harga_barang',
    ];
}
