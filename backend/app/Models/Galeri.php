<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan oleh model ini
    protected $table = 'galeri';

    // Tentukan kolom yang dapat diisi mass-assignment
    protected $fillable = [
        'judul', 
        'deskripsi', 
        'tanggal', 
        'gambar',
    ];

    // Tentukan kolom yang harus diperlakukan sebagai tanggal (untuk otomatis parsing)
    protected $dates = ['tanggal'];

    // Otomatis tambahkan atribut virtual
    protected $appends = ['gambar_url', 'gambar_list'];

    // Gambar utama (mengambil gambar pertama jika lebih dari satu)
    public function getGambarUrlAttribute()
    {
        $stored = $this->gambar;
        if (!$stored) {
            return null;
        }

        $decoded = json_decode($stored, true);
        $first = is_array($decoded) ? ($decoded[0] ?? null) : $stored;

        return $first ? asset('storage/' . $first) : null;
    }

    // Daftar semua gambar dalam bentuk URL
    public function getGambarListAttribute()
    {
        $stored = $this->gambar;
        if (!$stored) {
            return [];
        }

        $decoded = json_decode($stored, true);
        $paths = is_array($decoded) ? $decoded : [$stored];

        return array_map(fn ($path) => asset('storage/' . $path), $paths);
    }
}

