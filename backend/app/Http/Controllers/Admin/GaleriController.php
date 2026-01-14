<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GaleriController extends Controller
{
    public function __construct()
    {
        // Middleware untuk membatasi akses galeri hanya untuk admin
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            if (Auth::guard('admin')->user()->role !== 'admin') {
                return redirect()->route('admin.berita.index')->with('error', 'Anda tidak memiliki akses ke halaman galeri.');
            }
            return $next($request);
        });
    }

    // Menampilkan daftar galeri
    public function index()
    {
        $galeri = Galeri::latest()->get(); // Mengambil semua galeri terbaru
        return view('admin.galeri.index', compact('galeri'));
    }

    // Menampilkan form untuk menambah galeri
    public function create()
    {
        return view('admin.galeri.create');
    }

    // Menyimpan galeri baru ke database
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|array|max:10',
            'gambar.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'judul.required' => 'Judul galeri harus diisi.',
            'tanggal.required' => 'Tanggal galeri harus diisi.',
        ]);

        // Menyimpan gambar (banyak)
        $gambarPaths = [];
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                if (!$file) {
                    continue;
                }
                $gambarPaths[] = $file->storeAs(
                    'galeri',
                    time() . '-' . $file->getClientOriginalName(),
                    'public'
                );
            }
        }

        // Menyimpan galeri ke database
        Galeri::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => Carbon::parse($request->tanggal), // Convert to Carbon instance
            'gambar' => json_encode($gambarPaths),
        ]);

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit galeri
    public function edit(Galeri $galeri)
    {
        $galeri->tanggal = \Carbon\Carbon::parse($galeri->tanggal);
        return view('admin.galeri.edit', compact('galeri'));
    }

    // Memperbarui galeri di database
    public function update(Request $request, Galeri $galeri)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|array',
            'gambar.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'judul.required' => 'Judul galeri harus diisi.',
            'tanggal.required' => 'Tanggal galeri harus diisi.',
        ]);

        // Jika ada gambar baru, hapus gambar lama dan simpan gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus semua gambar lama
            if ($galeri->gambar) {
                $existing = json_decode($galeri->gambar, true);
                if (is_array($existing)) {
                    foreach ($existing as $path) {
                        if ($path && Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                } elseif (Storage::disk('public')->exists($galeri->gambar)) {
                    Storage::disk('public')->delete($galeri->gambar);
                }
            }

            // Simpan gambar baru (banyak)
            $gambarPaths = [];
            foreach ($request->file('gambar') as $file) {
                $gambarPaths[] = $file->storeAs(
                    'galeri',
                    time() . '-' . $file->getClientOriginalName(),
                    'public'
                );
            }
            $galeri->gambar = json_encode($gambarPaths);
        }

        // Update galeri
        $galeri->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => Carbon::parse($request->tanggal), // Convert to Carbon instance
        ]);

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil diperbarui.');
    }

    // Menghapus galeri
    public function destroy(Galeri $galeri)
    {
        // Hapus semua gambar dari storage jika ada (mendukung banyak gambar)
        if ($galeri->gambar) {
            $existing = json_decode($galeri->gambar, true);
            if (is_array($existing)) {
                foreach ($existing as $path) {
                    if ($path && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            } elseif (Storage::disk('public')->exists($galeri->gambar)) {
                Storage::disk('public')->delete($galeri->gambar);
            }
        }

        // Hapus galeri dari database
        $galeri->delete();

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil dihapus.');
    }
}

