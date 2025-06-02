@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Upload Tanda Tangan Direktur</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('direktur.ttd.upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="mb-3">
            <label for="ttd" class="form-label">File Tanda Tangan (PNG/JPG, max 2MB)</label>
            <input type="file" name="ttd" id="ttd" class="form-control @error('ttd') is-invalid @enderror" accept="image/png,image/jpeg">
            @error('ttd')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    @if($ttdPath)
        <div class="mb-3">
            <label class="form-label">Preview Tanda Tangan:</label><br>
            <img src="{{ asset('storage/' . $ttdPath) }}" alt="Tanda Tangan Direktur" style="max-width:300px; border:1px solid #ccc; padding:8px; background:#fff;">
        </div>
    @endif
</div>
@endsection 