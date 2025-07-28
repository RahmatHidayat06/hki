@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-5">
                            <h1 class="display-4 fw-bold text-primary mb-3">E-HAKCIPTA</h1>
                            <h2 class="h1 fw-bold text-warning mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">PASTI CEPAT</h2>
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf
                        
                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="nama_lengkap" class="form-label fw-semibold text-dark">Nama</label>
                                    <input id="nama_lengkap" type="text" 
                                           class="form-control form-control-lg @error('nama_lengkap') is-invalid @enderror" 
                                           name="nama_lengkap" value="{{ old('nama_lengkap') }}" 
                                           required autofocus placeholder="Nama">
                            @error('nama_lengkap')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                    <input id="email" type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" 
                                           required placeholder="Email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        </div>

                            <div class="row">
                        <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold text-dark">Password</label>
                            <div class="input-group">
                                        <input id="password" type="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                               name="password" required placeholder="Password">
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

                        <!-- Confirm Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold text-dark">Konfirmasi Password</label>
                            <div class="input-group">
                                        <input id="password_confirmation" type="password" 
                                               class="form-control form-control-lg" 
                                               name="password_confirmation" required placeholder="Password confirmation">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                            </div>

                            <div class="row">
                                <!-- No. KTP -->
                                <div class="col-md-6 mb-3">
                                    <label for="no_ktp" class="form-label fw-semibold text-dark">NIK</label>
                                    <input id="no_ktp" type="text" 
                                           class="form-control form-control-lg @error('no_ktp') is-invalid @enderror" 
                                           name="no_ktp" value="{{ old('no_ktp') }}" 
                                           required placeholder="No. KTP">
                                    @error('no_ktp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Telephone -->
                                <div class="col-md-6 mb-3">
                                    <label for="no_telp" class="form-label fw-semibold text-dark">Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white fw-bold">08</span>
                                        <input id="no_telp" type="text" 
                                               class="form-control form-control-lg @error('no_telp') is-invalid @enderror" 
                                               name="no_telp" value="{{ old('no_telp') }}" 
                                               required placeholder="Telephone">
                                    </div>
                                    @error('no_telp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Birth Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_lahir" class="form-label fw-semibold text-dark">Tanggal lahir</label>
                                    <input id="tanggal_lahir" type="date" 
                                           class="form-control form-control-lg @error('tanggal_lahir') is-invalid @enderror" 
                                           name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" 
                                           required>
                                    @error('tanggal_lahir')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Gender -->
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label fw-semibold text-dark">Jenis Kelamin</label>
                                    <div class="input-group">
                                        <select id="gender" class="form-select form-select-lg @error('gender') is-invalid @enderror" 
                                                name="gender" required>
                                            <option value="">Select...</option>
                                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        <span class="input-group-text">
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </div>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Nationality -->
                                <div class="col-md-6 mb-3">
                                    <label for="nationality" class="form-label fw-semibold text-dark">Kewarganegaraan</label>
                                    <div class="input-group">
                                        <select id="nationality" class="form-select form-select-lg @error('nationality') is-invalid @enderror" 
                                                name="nationality" required>
                                            <option value="">Select...</option>
                                            <option value="Indonesia" {{ old('nationality', 'Indonesia') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                            <option value="Malaysia" {{ old('nationality') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                            <option value="Singapura" {{ old('nationality') == 'Singapura' ? 'selected' : '' }}>Singapura</option>
                                            <option value="Thailand" {{ old('nationality') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                                            <option value="Philippines" {{ old('nationality') == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                                            <option value="Brunei" {{ old('nationality') == 'Brunei' ? 'selected' : '' }}>Brunei</option>
                                            <option value="Lainnya" {{ old('nationality') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        <span class="input-group-text">
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </div>
                                    @error('nationality')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Type of Applicant -->
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label fw-semibold text-dark">Type of Applicant</label>
                                    <div class="input-group">
                                        <select id="role" class="form-select form-select-lg @error('role') is-invalid @enderror" 
                                                name="role" required>
                                            <option value="">Select...</option>
                                            <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                            <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                        </select>
                                        <span class="input-group-text">
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </div>
                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Dosen-specific fields (conditionally shown) -->
                            <div class="row g-3 d-none" id="dosen-fields">
                                <!-- NIP/NIDN -->
                                <div class="col-md-6 mb-3">
                                    <label for="nip_nidn" class="form-label fw-semibold text-dark">NIP/NIDN</label>
                                    <input id="nip_nidn" type="text" 
                                           class="form-control form-control-lg @error('nip_nidn') is-invalid @enderror" 
                                           name="nip_nidn" value="{{ old('nip_nidn') }}" 
                                           placeholder="NIP/NIDN">
                                    @error('nip_nidn')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- ID Sinta -->
                                <div class="col-md-6 mb-3">
                                    <label for="id_sinta" class="form-label fw-semibold text-dark">ID Sinta</label>
                                    <input id="id_sinta" type="text" 
                                           class="form-control form-control-lg @error('id_sinta') is-invalid @enderror" 
                                           name="id_sinta" value="{{ old('id_sinta') }}" 
                                           placeholder="ID Sinta">
                                    @error('id_sinta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Username (Hidden, auto-generated) -->
                            <input type="hidden" name="username" id="username" value="{{ old('username') }}">

                            <!-- Register Button -->
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark py-3 rounded-pill">
                                    Register
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                                <p class="mb-0 text-muted">
                                    Already have an account? 
                                    <a href="{{ route('login') }}" class="text-warning fw-semibold text-decoration-none">
                                        Sign In
                                </a>
                            </p>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-generate username from email
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    
    emailInput.addEventListener('input', function() {
        const emailValue = this.value;
        if (emailValue.includes('@')) {
            const username = emailValue.split('@')[0];
            usernameInput.value = username;
        } else {
            usernameInput.value = emailValue;
        }
    });

    // Format phone number input
    const phoneInput = document.getElementById('no_telp');
    phoneInput.addEventListener('input', function() {
        // Remove all non-digit characters
        let value = this.value.replace(/\D/g, '');
        
        // Limit to 10 digits after '08'
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        
        this.value = value;
    });

    // Format KTP input
    const ktpInput = document.getElementById('no_ktp');
    ktpInput.addEventListener('input', function() {
        // Remove all non-digit characters
        let value = this.value.replace(/\D/g, '');
        
        // Limit to 16 digits
        if (value.length > 16) {
            value = value.substring(0, 16);
        }
        
        this.value = value;
    });

    // Toggle password visibility
    function setupPasswordToggle(passwordId, toggleId) {
        const togglePassword = document.querySelector('#' + toggleId);
        const password = document.querySelector('#' + passwordId);
        const eyeIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    }

    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('password_confirmation', 'togglePasswordConfirm');

    // Form validation feedback
    const form = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    function checkPasswordMatch() {
        if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Password tidak cocok');
            confirmPasswordInput.classList.add('is-invalid');
        } else {
            confirmPasswordInput.setCustomValidity('');
            confirmPasswordInput.classList.remove('is-invalid');
        }
    }

    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Birth date validation
    const birthDateInput = document.getElementById('tanggal_lahir');
    birthDateInput.addEventListener('change', function() {
        const today = new Date();
        const birthDate = new Date(this.value);
        const age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        if (age < 17) {
            this.setCustomValidity('Umur minimal 17 tahun');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });

    // Show/hide dosen-specific fields based on Type of Applicant selection
    const roleSelect = document.getElementById('role');
    const dosenFields = document.getElementById('dosen-fields');
    const nipNidnInput = document.getElementById('nip_nidn');
    const idSintaInput = document.getElementById('id_sinta');

    function toggleDosenFields() {
        if (roleSelect.value === 'dosen') {
            dosenFields.classList.remove('d-none');
            // Make fields required for dosen
            nipNidnInput.setAttribute('required', 'required');
            idSintaInput.setAttribute('required', 'required');
        } else {
            dosenFields.classList.add('d-none');
            // Remove required attribute for non-dosen
            nipNidnInput.removeAttribute('required');
            idSintaInput.removeAttribute('required');
            // Clear values when hidden
            nipNidnInput.value = '';
            idSintaInput.value = '';
        }
    }

    // Set initial state on page load
    toggleDosenFields();

    // Handle changes to role selection
    roleSelect.addEventListener('change', toggleDosenFields);

    // Handle form load with old values (for validation errors)
    if (roleSelect.value === 'dosen') {
        dosenFields.classList.remove('d-none');
    }
});
</script>
@endpush 