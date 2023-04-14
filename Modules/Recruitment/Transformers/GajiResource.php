<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class GajiResource extends Resource
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
            'notran' => $this->notran,
            'dari' => $this->dari,
            'sampai' => $this->sampai,
            'jns' => $this->jns,
            'tglbuat' => $this->tglbuat,
            'HARIX' => $this->HARIX,
            'sts' => $this->sts,
            'jgaji' => $this->jgaji,
            'kodeprs' => $this->kodeprs,
            'adaaoc' => $this->adaaoc,
            /*'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
