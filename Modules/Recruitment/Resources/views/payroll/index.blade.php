@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                {{-- Page Title --}}
                <div class="col-sm-6 d-none d-md-block">
                    <a class="text-white" href="#">
                        <h1>{{ __('Data Payroll') }}</h1>
                    </a>
                </div>
                <div class="col-sm-6 d-none d-md-block text-right">
                    {{-- <button class="btn btn-primary">Tambah</button> --}}
                </div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <payroll-table-records
                        url-data="{{ route('ajax.recruitment.get.payroll') }}"
                        url-store="{{ route('recruitment.payroll.store') }}"
                        url-import="{{ route('recruitment.import.payroll') }}"
                        url-rekapan-smm="{{ route('recruitment.download.rekapan.smm') }}"
                        url-rekapan-atm="{{ route('recruitment.download.rekapan.atm') }}"
                        url-permata-smm="{{ route('recruitment.download.permata.smm') }}"
                        url-permata-atm="{{ route('recruitment.download.permata.atm') }}"
                        url-excell-smm="{{ route('recruitment.download.excell.smm') }}"
                        url-excell-atm="{{ route('recruitment.download.excell.atm') }}"
                        url-full-smm="{{ route('recruitment.download.full.smm') }}"
                        url-full-atm="{{ route('recruitment.download.full.atm') }}"
                        url-pph-smm="{{ route('recruitment.download.pph.smm') }}"
                        url-pph-atm="{{ route('recruitment.download.pph.atm') }}"
                        url-cetak="{{ route('recruitment.print.slip') }}"
                        {{--url-change-status="{{ route('recruitment.pph.store') }}"
                        url-check-name-exist="{{ route('ajax.general.check.unit.name.exist') }}"
                        url-check-symbol-exist="{{ route('ajax.general.check.unit.symbol.exist') }}"--}}
                    ></payroll-table-records>
                </div>
            <div>
        </div>
    </section>
@endsection

