<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class PayrollResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'notrans' => $this->notrans,
            'kantor' => $this->kantor,
            'bagian' => $this->bagian,
            'nrp' => $this->nrp,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'stapajak' => $this->stapajak,
            'npwp' => $this->npwp,
            'jumlahhari' => $this->jumlahhari,
            'gapok' => number_format($this->gapok),
            'premihadir' => number_format($this->premihadir),
            'premiprod' => $this->premiprod,
            'uangjabatan' => $this->uangjabatan,
            'bonus' => $this->bonus,
            'gaji' => number_format($this->gaji),
            'jkm' => $this->jkm,
            'jkk' => $this->jkk,
            'bpjskes' => number_format($this->bpjskes),
            'bpjskeskar' => number_format($this->bpjskeskar),
            'h' => $this->h,
            's' => $this->s,
            'i' => $this->i,
            'a' => $this->a,
            'c' => $this->c,
            'off' => $this->off,
            'st' => $this->st,
            'telat_kurang' => $this->telat_kurang,
            'telat_lebih' => $this->telat_lebih,
            'setengah_hari' => $this->setengah_hari,
            'nilai_s' => $this->nilai_s,
            'nilai_i' => $this->nilai_i,
            'nilai_a' => $this->nilai_a,
            'nilai_c' => $this->nilai_c,
            'nilai_off' => $this->nilai_off,
            'nilai_st' => $this->nilai_st,
            'nilai_telat_kurang' => $this->nilai_telat_kurang,
            'nilai_telat_lebih' => $this->nilai_telat_lebih,
            'nilai_setengah_hari' => $this->nilai_setengah_hari,
            'totalpotonganabsensi' => number_format($this->totalpotonganabsensi),
            'jumlahlembur1' => $this->jumlahlembur1,
            'tariflembur1' => $this->tariflembur1,
            'uanglembur1' => $this->uanglembur1,
            'jumlahlembur2' => $this->jumlahlembur2,
            'tariflembur2' => $this->tariflembur2,
            'uanglembur2' => $this->uanglembur2,
            'jumlahlembur2minggu' => $this->jumlahlembur2minggu,
            'tariflembur2minggu' => $this->tariflembur2minggu,
            'uanglembur2minggu' => $this->uanglembur2minggu,
            'jumlahlembur3minggu' => $this->jumlahlembur3minggu,
            'tariflembur3minggu' => $this->tariflembur3minggu,
            'uanglembur3minggu' => $this->uanglembur3minggu,
            'totallembur' => number_format($this->totallembur),
            'bruto' => number_format($this->bruto),
            'potkoperasi' => number_format($this->potkoperasi),
            'potjhtkar' => number_format($this->potjhtkar),
            'potpensiunkar' => number_format($this->potpensiunkar),
            'potlain' => $this->potlain,
            'totalpotongan' => number_format($this->totalpotongan),
            'netto' => number_format($this->netto),
            'norek' => number_format($this->norek),
            'periode_awal' => $this->periode_awal,
            'periode_akhir' => $this->periode_akhir,
            'date_created' => $this->date_created,
            'bulan' => date('M', strtotime($this->date_created)),
            'tahun' => date('Y', strtotime($this->date_created)),
            /*'urlimportPayroll' => route('recruitment.import.payroll'),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
