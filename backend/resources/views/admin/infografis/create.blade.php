<!-- resources/views/admin/infografis/create.blade.php -->

@extends('layouts.admin')

@section('title', 'Tambah Infografis')

@section('content')
    <div class="container my-5">
        <h2 class="text-center mb-4">Tambah Infografis</h2>

        <form action="{{ route('admin.infografis.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi (opsional)</label>
                <textarea name="deskripsi" class="form-control" placeholder="Boleh dikosongkan"></textarea>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label d-block">Gambar (maksimal 3 gambar)</label>
                <small class="text-muted d-block mb-2">Pilih hingga 3 gambar. Setiap kolom bersifat opsional.</small>
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
                <input type="file" name="gambar[]" class="form-control mb-2" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection
