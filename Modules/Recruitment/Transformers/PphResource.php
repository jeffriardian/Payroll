<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class PphResource extends Resource
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
            'nrp' => $this->nrp,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'ptkp_code' => $this->ptkp_code,
            'gaji' => number_format($this->gaji),
            'jaminan_perusahaan' => number_format($this->jaminan_perusahaan),
            'bruto' => number_format($this->bruto),
            'potongan' => number_format($this->potongan),
            'netto_sebulan' => number_format($this->netto_sebulan),
            'netto_setahun' => number_format($this->netto_setahun),
            'ptkp' => number_format($this->ptkp),
            'pkp' => number_format($this->pkp),
            'pph21_terutang_setahun' => number_format($this->pph21_terutang_setahun),
            'pph21_terutang_sebulan' => number_format($this->pph21_terutang_sebulan),
            'periode_bulan' => $this->periode_bulan,
            'periode_tahun' => $this->periode_tahun,
            /*'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
