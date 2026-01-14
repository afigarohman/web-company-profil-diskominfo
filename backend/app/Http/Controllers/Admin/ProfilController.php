<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('admin')->user();
            if ($user->role !== 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $profils = Profil::all();
        return view('admin.profil.index', compact('profils'));
    }

    public function create()
    {
        $categories = $this->categories();
        return view('admin.profil.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'title' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'konten' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['kategori', 'title', 'tanggal', 'konten']);

        if ($request->hasFile('pdf')) {
            $data['pdf'] = $request->file('pdf')->store('profil', 'public');
        }

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('profil', 'public');
        }

        Profil::create($data);

        return redirect()->route('admin.profil.index')->with('success', 'Profil berhasil ditambahkan.');
    }

    public function edit(Profil $profil)
    {
        $categories = $this->categories();
        return view('admin.profil.edit', compact('profil', 'categories'));
    }

    public function update(Request $request, Profil $profil)
    {
        $request->validate([
            'kategori' => 'required|string',
            'title' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'konten' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['kategori', 'title', 'tanggal', 'konten']);

        if ($request->hasFile('pdf')) {
            if ($profil->pdf) {
                Storage::disk('public')->delete($profil->pdf);
            }
            $data['pdf'] = $request->file('pdf')->store('profil', 'public');
        }

        if ($request->hasFile('gambar')) {
            if ($profil->gambar) {
                Storage::disk('public')->delete($profil->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('profil', 'public');
        }

        $profil->update($data);

        return redirect()->route('admin.profil.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function destroy(Profil $profil)
    {
        if ($profil->pdf) {
            Storage::disk('public')->delete($profil->pdf);
        }
        if ($profil->gambar) {
            Storage::disk('public')->delete($profil->gambar);
        }
        $profil->delete();

        return redirect()->route('admin.profil.index')->with('success', 'Profil berhasil dihapus.');
    }

    private function categories()
    {
        // Key = nilai yang disimpan di kolom `kategori` (dipakai API & frontend)
        // Value = label yang tampil di dropdown admin
        return [
            'sekilas-dinas'       => 'Sekilas Dinas',
            'visi-misi'           => 'Visi & Misi',
            'struktur-organisasi' => 'Struktur Organisasi',
            'tugas-pokok-fungsi'  => 'Tugas Pokok & Fungsi',
            'standar-pelayanan'   => 'Standar Pelayanan',
            'kantor-dinas'        => 'Kantor Dinas',
            'tentang'             => 'Tentang (Profil Lengkap)',
        ];
    }
} 