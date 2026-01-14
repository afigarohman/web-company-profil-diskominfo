@extends('layouts.admin')

@section('title', 'Edit Galeri')

@section('content')
    <div class="container my-5">
        <h2 class="text-center mb-4">Edit Galeri</h2>

        <form action="{{ route('admin.galeri.update', $galeri) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" value="{{ old('judul', $galeri->judul) }}" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $galeri->deskripsi) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $galeri->tanggal->format('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Baru (opsional, akan mengganti semua gambar lama)</label>
                <small class="text-muted d-block mb-2">Jika tidak memilih gambar baru, gambar lama akan tetap digunakan.</small>
                <input type="file" name="gambar[]" id="gambar" class="form-control" multiple accept="image/*">
                @if ($galeri->gambar_list && count($galeri->gambar_list) > 0)
                    <div class="mt-3">
                        <p class="text-muted small mb-2">Gambar saat ini:</p>
                        <div class="row">
                            @foreach ($galeri->gambar_list as $gambarUrl)
                                <div class="col-md-3 mb-2">
                                    <img src="{{ $gambarUrl }}" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.galeri.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection

