<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Recruitment\Entities\AbsensiPayrollStaff;

class AbsensiPayrollStaffImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AbsensiPayrollStaff([
            'kantor' => $row['kantor'],
            'nrp' => $row['nrp'],
            'bagian' => $row['bagian'],
            'nik' => $row['nik'],
            'nama' => $row['nama1'],
            'stapajak' => $row['stapajak'],
            'jenis_gaji' => $row['jenis_gaji'],
            'jhari' => $row['jhari'],
            'gapok' => $row['gapok'],
            'uang_hadir' => $row['uang_hadir'],
            'uang_prestasi' => $row['uang_prestasi'],
            'bruto_gaji' => $row['bruto_gaji'],
            'jkm' => $row['jkm'],
            'jkk' => $row['jkk'],
            'bpjskes' => $row['bpjskes'],
            'bpjskeskar' => $row['bpjskeskar'],
            'hari' => $row['h'],
            'sakit' => $row['s'],
            'ijin' => $row['i'],
            'alpa' => $row['a'],
            'cuti' => $row['c'],
            'off' => $row['off'],
            'st' => $row['st'],
            'telat_kurang' => $row['telat_kurang'],
            'telat_lebih' => $row['telat_lebih'],
            'setengah_hari' => $row['setengah_hari'],
            'nilai_s' => $row['nilai_s'],
            'nilai_i' => $row['nilai_i'],
            'nilai_a' => $row['nilai_a'],
            'nilai_c' => $row['nilai_c'],
            'nilai_off' => $row['nilai_off'],
            'nilai_st' => $row['nilai_st'],
            'nilaitelatkurang' => $row['nilaitelatkurang'],
            'nilaitelatlebih' => $row['nilaitelatlebih'],
            'nilaisetengahhari' => $row['nilaisetengahhari'],
            'totalpotonganabsensi' => $row['totalpotonganabsensi'],
            'jam_kerja' => $row['jam_kerja'],
            'lembur' => $row['lembur'],
            'l1' => $row['l1'],
            'tarif_l1' => $row['tarif_l1'],
            'nilai_l1' => $row['nilai_l1'],
            'l2' => $row['l2'],
            'tarif_l2' => $row['tarif_l2'],
            'nilai_l2' => $row['nilai_l2'],
            'l2minggu' => $row['l2minggu'],
            'tarifl2minggu' => $row['tarifl2minggu'],
            'nilail2minggu' => $row['nilail2minggu'],
            'l3minggu' => $row['l3minggu'],
            'tarifl3minggu' => $row['tarifl3minggu'],
            'nilail3minggu' => $row['nilail3minggu'],
            'total_lembur' => $row['total_lembur'],
            'koperasi' => $row['koperasi'],
            'jht' => $row['jht'],
            'pensiun' => $row['pensiun'],
            'total_potongan' => $row['total_potongan'],
            'total_gaji' => $row['total_gaji'],
            'nama_1' => $row['nama_1'],
            'norek' => $row['norek'],
        ]);
    }

    //LIMIT CHUNKSIZE
    public function chunkSize(): int
    {
        return 1000; //ANGKA TERSEBUT PERTANDA JUMLAH BARIS YANG AKAN DIEKSEKUSI
    }
}
