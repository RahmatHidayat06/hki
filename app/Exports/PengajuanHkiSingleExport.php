<?php

namespace App\Exports;

use App\Models\PengajuanHki;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengajuanHkiSingleExport implements FromCollection, WithHeadings
{
    protected PengajuanHki $pengajuan;

    public function __construct(PengajuanHki $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function collection()
    {
        $rows = [];

        // Iterate creators (pencipta)
        foreach ($this->pengajuan->pengaju as $creator) {
            $rows[] = [
                $this->pengajuan->nomor_pengajuan,
                $this->pengajuan->judul_karya,
                $creator->nama,
                $creator->email,
                $creator->no_telp,
                $creator->kewarganegaraan,
                $creator->kodepos,
                $this->pengajuan->identitas_ciptaan,
                $this->pengajuan->sub_jenis_ciptaan,
                $this->pengajuan->tahun_usulan,
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Nomor Pengajuan',
            'Judul Karya',
            'Nama Pencipta',
            'Email Pencipta',
            'No Telp',
            'Kewarganegaraan',
            'Kode Pos',
            'Identitas Ciptaan',
            'Sub Jenis Ciptaan',
            'Tahun Usulan',
        ];
    }
} 