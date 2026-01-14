<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory;
    protected $fillable = [
        'kategori', 'judul', 'title', 'konten', 'pdf', 'gambar', 'tanggal'
    ];

    // Otomatis tambahkan URL file ke response API
    protected $appends = ['pdf_url', 'gambar_url'];

    public function getPdfUrlAttribute()
    {
        if (!$this->pdf) {
            return null;
        }

        return asset('storage/' . $this->pdf);
    }

    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }

        return asset('storage/' . $this->gambar);
    }
} 