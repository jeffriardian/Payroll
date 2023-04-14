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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"> Tahun
                                @foreach($tahun1 as $tahun1)
                                    {{ $tahun1->tahun_kemarin }}
                                @endforeach
                            </h3>
                        </div>
                        <div class="card-body">
                            <table border="1" width="100%">
                                <tr>
                                    <th width="5%"><center>No.</center></th>
                                    <th width="70%"><center>Bulan</center></th>
                                    <th width="25%"><center>Status</center></th>
                                </tr>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach($query1 as $query1)
                                <tr>
                                    <td><center>{{ $no++ }}</center></td>
                                    <td><center>{{ $query1->text }}</center></td>
                                    <td><center>
                                        @if($query1->status <> '')
                                            <input type="checkbox" name="status[]" checked>
                                        @else
                                            <input type="checkbox" name="status[]" unchecked>
                                        @endif
                                    </center></td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"> Tahun
                                @foreach($tahun as $tahun)
                                    {{ $tahun->tahun_sekarang }}
                                @endforeach
                            </h3>
                        </div>
                        <div class="card-body">
                            <table border="1" width="100%">
                                <tr>
                                    <th width="5%"><center>No.</center></th>
                                    <th width="70%"><center>Bulan</center></th>
                                    <th width="25%"><center>Status</center></th>
                                </tr>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach($query as $query)
                                <tr>
                                    <td><center>{{ $no++ }}</center></td>
                                    <td><center>{{ $query->text }}</center></td>
                                    <td><center>
                                        @if($query->status <> '')
                                            <input type="checkbox" name="status[]" checked>
                                        @else
                                            <input type="checkbox" name="status[]" unchecked>
                                        @endif
                                    </center></td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
        </section>
    </section>
@endsection
