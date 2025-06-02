@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Ciptaan (Draft)</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Karya</th>
                <th>Kategori</th>
                <th>Jenis Ciptaan</th>
                <th>Sub Jenis Ciptaan</th>
                <th>Tahun Usulan</th>
                <th>Jumlah Pencipta</th>
                <th>Status</th>
                <th>Tanggal Draft</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($drafts as $draft)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $draft->judul_karya }}</td>
                <td>{{ $draft->kategori }}</td>
                <td>{{ $draft->identitas_ciptaan }}</td>
                <td>{{ $draft->sub_jenis_ciptaan }}</td>
                <td>{{ $draft->tahun_usulan ?? '-' }}</td>
                <td>{{ $draft->jumlah_pencipta }}</td>
                <td><span class="badge bg-secondary">Draft</span></td>
                <td>{{ $draft->created_at ? $draft->created_at->format('d/m/Y H:i') : '-' }}</td>
                <td>
                    <a href="{{ route('draft.edit', $draft->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('draft.destroy', $draft->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus draft ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Belum ada draft.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 