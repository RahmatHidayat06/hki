@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: #e9f0fa; margin:0; padding:0; width:100vw;">
    <div class="w-100 d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 px-0">
            <div class="card shadow rounded-4 border-0 p-3 mx-0">
                <div class="text-center mb-3">
                    <img src="/img/logo-hki.png" alt="Logo HKI" class="mb-2" style="max-width:120px;">
                    <h4 class="fw-bold mb-1" style="color:#0a2a6c;">Selamat Datang</h4>
                    <div class="text-muted mb-2">Silakan login untuk melanjutkan</div>
                </div>
                <div class="card-body p-0">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="login" class="form-label">Username atau Email</label>
                            <input id="login" type="text" class="form-control form-control-lg @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autofocus>
                            @error('login')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                Login
                            </button>
                        </div>

                        <!-- Forgot Password Link -->
                        <div class="text-center mb-3">
                            <a href="{{ route('password.request') }}" class="text-muted text-decoration-none">
                                <i class="fas fa-key me-1"></i>Lupa Password?
                            </a>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="mb-0">Belum punya akun? 
                                <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">
                                    Daftar di sini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // toggle the eye icon
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
});
</script>
@endpush