@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                {{-- Page Title --}}
                <div class="col-sm-6 d-none d-md-block">
                    <a class="text-white" href="#">
                        <h1>{{ __('Data THR') }}</h1>
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
                    <thr-table-records
                        url-data="{{ route('ajax.recruitment.get.thr') }}"
                        url-store="{{ route('recruitment.thr.store') }}"
                        url-import="{{ route('recruitment.import.thr') }}"
                        {{--url-change-status="{{ route('recruitment.pph.store') }}"
                        url-check-name-exist="{{ route('ajax.general.check.unit.name.exist') }}"
                        url-check-symbol-exist="{{ route('ajax.general.check.unit.symbol.exist') }}"--}}
                    ></thr-table-records>
                </div>
            <div>
        </div>
    </section>
@endsection

