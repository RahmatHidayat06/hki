# Header Modern - Sistem Pengajuan HKI

## Overview
Header baru telah didesain ulang untuk memberikan pengalaman pengguna yang lebih modern, profesional, dan konsisten di seluruh aplikasi.

## Fitur Header Baru

### 1. **Header Utama (Fixed Position)**
- **Posisi:** Fixed di bagian atas dengan z-index tinggi
- **Konten:**
  - Logo P3M di kiri
  - Judul sistem: "Sistem Pengajuan HKI - Universitas Teknologi Digital Indonesia"
  - Clock & Date real-time (desktop only)
  - Notifikasi dengan counter
  - User profile dropdown

### 2. **Breadcrumb & Page Title Section**
- **Breadcrumb Navigation:** Menunjukkan lokasi halaman saat ini
- **Page Title:** Judul halaman dengan icon dan deskripsi
- **Quick Actions:** Area untuk tombol-tombol aksi cepat

### 3. **Responsive Design**
- **Desktop:** Header penuh dengan semua elemen
- **Mobile:** Header compact dengan elemen penting saja
- **Tablet:** Header sedang dengan penyesuaian ukuran

## Komponen Header

### 1. **Logo & Branding**
```html
<div class="header-logo me-3">
    <img src="/img/logo-hki.png" alt="P3M Logo" style="height: 40px;">
</div>
<div class="header-title">
    <h5>Sistem Pengajuan HKI</h5>
    <small>Universitas Teknologi Digital Indonesia</small>
</div>
```

### 2. **Real-time Clock**
- Format: HH:MM:SS
- Update setiap detik
- Menggunakan format Indonesia
- Auto-hide di mobile

### 3. **Notification System**
- Bell icon dengan counter
- Dropdown preview (3 notifikasi terbaru)
- Link ke halaman notifikasi lengkap
- Real-time counter update

### 4. **User Profile Dropdown**
- Avatar dengan inisial nama
- Nama user (truncated di mobile)
- Link ke profile
- Logout button

## Cara Menggunakan Page Header

### Basic Usage
```blade
<x-page-header 
    title="Judul Halaman" 
    description="Deskripsi halaman"
    icon="fas fa-icon"
/>
```

### Dengan Breadcrumbs
```blade
<x-page-header 
    title="Detail Pengajuan" 
    description="Lihat detail pengajuan HKI"
    icon="fas fa-file-alt"
    :breadcrumbs="[
        ['title' => 'Pengajuan', 'url' => route('pengajuan.index')],
        ['title' => 'Detail']
    ]"
/>
```

### Dengan Quick Actions
```blade
<x-page-header 
    title="Pengajuan HKI" 
    description="Kelola pengajuan Anda"
    icon="fas fa-file-alt"
>
    <div class="d-flex gap-2">
        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Baru
        </a>
        <a href="#" class="btn btn-outline-secondary">
            <i class="fas fa-download me-2"></i>Export
        </a>
    </div>
</x-page-header>
```

## Styling & Customization

### CSS Classes Utama
- `.modern-header` - Container header utama
- `.header-btn` - Style untuk tombol di header
- `.header-datetime` - Container clock & date
- `.breadcrumb-item` - Style breadcrumb items

### Color Scheme
- **Primary:** #0d6efd (Bootstrap Blue)
- **Background:** Linear gradient white to light gray
- **Text:** Dark gray (#212529)
- **Accent:** Various status colors

### Animations
- **Dropdown:** Fade in from top
- **Buttons:** Hover lift effect
- **Clock:** Smooth transitions

## Layout Integration

### Sidebar Adjustment
- Header height: 120px (desktop), 100px (mobile)
- Sidebar positioned below header
- Main content adjusted for header height
- Mobile overlay system maintained

### Z-Index Hierarchy
- Header: 1000
- Sidebar: 999
- Dropdowns: 1001+
- Mobile overlay: 998

## Browser Compatibility
- **Modern Browsers:** Full support dengan semua fitur
- **Mobile Browsers:** Responsive design
- **Legacy Browsers:** Graceful degradation

## Performance Optimizations
- Minimal JavaScript untuk clock
- CSS animations hardware-accelerated
- Optimized dropdown rendering
- Efficient notification loading

## Update Notes
- Menghapus duplicate header elements
- Unified notification system
- Consistent breadcrumb navigation
- Mobile-first responsive design
- Improved accessibility

## Maintenance
- Clock updates setiap detik
- Notification counter real-time
- Auto-close alerts after 5 detik
- Sidebar state management
- Responsive breakpoint handling 