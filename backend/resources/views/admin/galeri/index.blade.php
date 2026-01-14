@extends('layouts.admin')

@section('title', 'Daftar Galeri')

@section('content')
    <div class="container my-5">
        <h2 class="text-center mb-4">Galeri Foto</h2>
        
        <!-- Tombol untuk menambah galeri baru -->
        <div class="text-right mb-4">
            <a href="{{ route('admin.galeri.create') }}" class="btn btn-primary">Tambah Galeri</a>
        </div>

        <!-- Menampilkan daftar galeri -->
        <div class="row">
            @foreach ($galeri as $galeriItem)
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <!-- Menampilkan gambar galeri -->
                        @if ($galeriItem->gambar_url)
                            <img src="{{ $galeriItem->gambar_url }}" class="card-img-top" alt="Galeri" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <!-- Menampilkan judul dan tanggal galeri -->
                            <h5 class="card-title">{{ $galeriItem->judul }}</h5>
                            <p class="card-text text-muted small">
                                {{ \Carbon\Carbon::parse($galeriItem->tanggal)->format('d M Y') }} | Admin
                            </p>
                            @if ($galeriItem->deskripsi)
                                <p class="card-text text-muted small">{{ Str::limit($galeriItem->deskripsi, 100) }}</p>
                            @endif
                            
                            <!-- Tombol Edit dan Hapus -->
                            <div class="mt-3">
                                <a href="{{ route('admin.galeri.edit', $galeriItem) }}" class="btn btn-warning btn-sm">Edit</a>

                                <!-- Form untuk menghapus galeri -->
                                <form action="{{ route('admin.galeri.destroy', $galeriItem) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

