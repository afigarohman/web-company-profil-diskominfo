@extends('layouts.admin')

@section('title', 'Tambah Galeri')

@section('content')
    <div class="container my-5">
        <h2 class="text-center mb-4">Tambah Galeri</h2>

        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi (opsional)</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Boleh dikosongkan"></textarea>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label d-block">Gambar (maksimal 10 gambar)</label>
                <small class="text-muted d-block mb-2">Pilih hingga 10 gambar. Setiap kolom bersifat opsional.</small>
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.galeri.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection

