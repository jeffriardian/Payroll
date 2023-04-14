<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Recruitment\Entities\PesangonUpload;

class PesangonImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PesangonUpload([
            'kantor' => $row['kantor'],
            'nik' => $row['nik'],
            'nama' => $row['nama1'],
            'pesangon' => $row['pesangon'],
            'nama_rekening' => $row['nama_1'],
            'norek' => $row['norek'],
        ]);
    }

    //LIMIT CHUNKSIZE
    public function chunkSize(): int
    {
        return 1000; //ANGKA TERSEBUT PERTANDA JUMLAH BARIS YANG AKAN DIEKSEKUSI
    }
}
