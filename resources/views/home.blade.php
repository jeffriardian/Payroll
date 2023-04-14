@extends('layouts.app')

@section('content')

    {{-- Page Header --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                {{-- Page Title --}}
                <div class="col-sm-6 d-none d-md-block">
                    <a class="text-white" href="#">
                        <h1>{{ __('Dashboard') }}</h1>
                    </a>
                </div>
                <div class="col-sm-6 d-none d-md-block text-right">
                    {{-- <button class="btn btn-primary">Tambah</button> --}}
                </div>
            </div>
        </div>
    </section>


    <section class="content">
        <section class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aktivitas Stok</h3>
                        </div>
                        <div class="card-body">
                            -
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Motivasi / Pengumuman</h3>
                        </div>
                        <div class="card-body">
                            -
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Notifikasi</h3>
                            </div>
                            <div class="card-body">
                                -
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Barang Sering Keluar Masuk</h3>
                            </div>
                            <div class="card-body">
                                -
                            </div>
                        </div>
                    </div>
            </div>
        </section>
    </section>
@endsection
