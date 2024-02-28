<?php

namespace App\Exports;

use App\Models\Pangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TabelHargaExport implements FromQuery, WithMapping, ShouldAutoSize, WithHeadings, WithStyles
{
  
    protected $isAdmin;
    protected $filter;

    public function __construct($isAdmin, $filter)
    {
        $this->isAdmin = $isAdmin;
        $this->filter = $filter;
    }

    public function query()
    {

        // $isAdmin = auth()->user()->is_admin;

        // if ($isAdmin) {
        //     return Pangan::with(['barang.komoditas'])->orderBy('komoditas_id', 'ASC')->orderBy('barang_id', 'ASC')->orderBy('created_at', 'desc');
        // } else {
        //     $operator = auth()->user()->operator;
        //     return Pangan::with(['barang.komoditas'])->where('pasar', $operator)->orderBy('komoditas_id', 'ASC')->orderBy('barang_id', 'ASC')->orderBy('created_at', 'desc');
        // }

        $panganQuery = Pangan::query();

        if ($this->isAdmin) {
            // Jika admin, ekspor semua data harga
            // Jika ada filter pasar yang dipilih, terapkan filter
            if ($this->filter) {
                $panganQuery->where('pasar', 'like', '%' . $this->filter . '%');
            }
        } else {
            // Jika bukan admin, hanya ekspor data harga dari pasar operator yang sesuai
            $operator = auth()->user()->operator;
            $panganQuery->where('pasar', $operator);
        }

        // Ambil data harga sesuai dengan kondisi yang ditentukan
        return $panganQuery->with(['barang.komoditas'])->orderBy('komoditas_id', 'ASC')->orderBy('barang_id', 'ASC')->orderBy('created_at', 'desc');
 
    }    

    public function headings(): array
    {
        return [
            'Komoditas',
            'Jenis Barang',   
            'Satuan',
            'Harga Lama',
            'Harga Sekarang',
            'Perubahan (Rp)',
            'Perubahan (%)',
            'Keterangan',
            'Periode'
        ];
    }

    public function map($pangan): array
    {

        
        return [
            $pangan->barang->komoditas->nama,
            $pangan->barang->nama,
            $pangan->satuan,
            $pangan->harga_sebelum,
            $pangan->harga,
            $pangan->perubahan_rp,
            $pangan->perubahan_persen,
            $pangan->keterangan,
            Carbon::parse($pangan->periode)->format('d/M/Y')
            
            
        ];
    }

    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

           
        ];
    }
}
