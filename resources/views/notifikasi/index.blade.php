@extends('layouts.app')

@section('content')
<x-page-header 
    title="Notifikasi" 
    description="Kelola notifikasi sistem"
    icon="fas fa-bell"
/>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Notifikasi') }}</h5>
                    @if($notifikasi->where('dibaca', false)->count() > 0)
                        <form action="{{ route('notifikasi.markAllAsRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($notifikasi->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Tidak ada notifikasi</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($notifikasi as $item)
                                <div class="list-group-item list-group-item-action {{ !$item->dibaca ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $item->judul }}</h6>
                                        <small>{{ $item->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $item->pesan }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            Status: {{ $item->pengajuanHki->status ?? 'Tidak tersedia' }}
                                        </small>
                                        <div>
                                            @php
                                                $actionRoute = null;
                                                $actionLabel = null;
                                                if(auth()->user()->role === 'admin'){
                                                    if(Str::contains($item->judul, 'Divalidasi')){
                                                        $actionRoute = route('admin.pengajuan.show', $item->pengajuan_hki_id);
                                                        $actionLabel = 'Finalisasi';
                                                    }elseif(Str::contains($item->judul, 'Verifikasi Pembayaran')){
                                                        $actionRoute = route('admin.pengajuan.show', $item->pengajuan_hki_id);
                                                        $actionLabel = 'Verifikasi';
                                                    }
                                                }
                                            @endphp
                                            @if($actionRoute)
                                                <a href="{{ $actionRoute }}" class="btn btn-sm btn-primary me-2">
                                                    {{ $actionLabel }}
                                                </a>
                                            @endif
                                            @if(!$item->dibaca)
                                                <form action="{{ route('notifikasi.markAsRead', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                                                        Tandai Dibaca
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('notifikasi.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $notifikasi->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 