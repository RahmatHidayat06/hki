<?php

namespace App\Exports;

use App\Models\PengajuanHki;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengajuanHkiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PengajuanHki::select(
            'nomor_pengajuan',
            'judul_karya',
            'kategori',
            'deskripsi',
            'nama_pengusul',
            'nip_nidn',
            'no_hp',
            'id_sinta',
            'jumlah_pencipta',
            'identitas_ciptaan',
            'sub_jenis_ciptaan',
            'tanggal_pertama_kali_diumumkan',
            'tahun_usulan',
            'role',
            'status',
            'tanggal_pengajuan'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Pengajuan',
            'Judul Karya',
            'Kategori',
            'Deskripsi',
            'Nama Pengusul',
            'NIP/NIDN',
            'No HP',
            'ID Sinta',
            'Jumlah Pencipta',
            'Identitas Ciptaan',
            'Sub Jenis Ciptaan',
            'Tanggal Pertama Kali Diumumkan',
            'Tahun Usulan',
            'Role',
            'Status',
            'Tanggal Pengajuan',
        ];
    }
} 