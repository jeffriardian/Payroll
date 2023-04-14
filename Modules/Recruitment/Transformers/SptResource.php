<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class SptResource extends Resource
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
            'gross_yearly' => number_format($this->gross_yearly),
            'position_cost_yearly' => number_format($this->position_cost_yearly),
            'jht_employee_yearly' => number_format($this->jht_employee_yearly),
            'jp_employee_yearly' => number_format($this->jp_employee_yearly),
            'jht_yearly' => number_format($this->jht_yearly),
            'netto_yearly' => number_format($this->netto_yearly),
            'ptkp' => number_format($this->ptkp),
            'pkp' => number_format($this->pkp),
            'tax_allowance_salary' => number_format($this->tax_allowance_salary),
            'pph_salary' => number_format($this->pph_salary),
            'tax_allowance_bonus' => number_format($this->tax_allowance_bonus),
            'pph_bonus' => number_format($this->pph_bonus),
            'tax_allowance_thr' => number_format($this->tax_allowance_thr),
            'pph_thr' => number_format($this->pph_thr),
            'tax_allowance_pesangon' => number_format($this->tax_allowance_pesangon),
            'pph_pesangon' => number_format($this->pph_pesangon),
            'pph_due' => number_format($this->pph_due),
            'pph_paid' => number_format($this->pph_paid),
            'periode_tahun' => $this->periode_tahun,
            'url_print' => route('recruitment.print.spt', [$this->id]),
            /*'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
