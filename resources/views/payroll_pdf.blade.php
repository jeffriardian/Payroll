<!DOCTYPE html>
<html>
<head>
	<title>Membuat Laporan PDF Dengan DOMPDF Laravel</title>
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 8.5pt;
		}
	</style>

    <table width="100%" style="padding-top:5;style=padding-bottom:5;style=padding-right:5;style=padding-left:5">
        <tr>
            @php $i=0 @endphp
            @foreach($payroll as $p)
                <td>
                    <table width="90%" style="border:1px solid black">
                        <tr>
                            <td colspan="3">
                                <strong>
                                <center>
                                    Slip Gaji {{date('F Y', strtotime($p->date_created))}}
                                    @if(substr($p->nik, 0, 2) == 01)
                                        (SMM)
                                    @else
                                        (Amidis)
                                    @endif
                                </center>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10"><strong>NIK</strong></td>
                            <td><strong>:</strong></td>
                            <td align="right" style="padding-right:10"><strong>{{ $p->nik }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-left:10"><strong>Nama</strong></td>
                            <td><strong>:</strong></td>
                            <td align="right" style="padding-right:10"><strong>{{ $p->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3">---------------------------------------------------------------------------------</td>
                        </tr>
                        <tr>
                            <td style="padding-left:10">Gapok</td>
                            <td>:</td>
                            <td align="right" style="padding-right:10">{{number_format($p->gapok,0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:10">Uang Hadir</td>
                            <td>:</td>
                            <td align="right" style="padding-right:10">{{number_format($p->premihadir,0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:10">Uang Prestasi</td>
                            <td>:</td>
                            <td align="right" style="padding-right:10">{{number_format($p->premiprod,0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:10">Uang Lembur</td>
                            <td>:</td>
                            <td align="right" style="padding-right:10">{{number_format($p->totallembur,0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">---------------------------------------------------------------------------------</td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><strong><center>Gaji Bruto</center></strong></td>
                            <td align="right" style="padding-right:10">{{number_format($p->Bruto,0,",",".")}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td style="padding-left:10">Pot. Absensi</td>
                            <td>:</td>
                            <td align="right" style="padding-right:10">{{number_format($p->totalpotonganabsensi,0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:10">Koperasi</td>
                            <td>:</td>
                            <td align="right" style="padding-right:10">{{number_format($p->potkoperasi,0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:10">BPJS</td>
                            <td>:</td>
                            <td align="right" style="padding-right:10">{{number_format(($p->potbpjstkkar+$p->potbpjskeskar+$p->pensiunkar),0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">---------------------------------------------------------------------------------</td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><strong><center>Total Potongan</center></strong></td>
                            <td align="right" style="padding-right:10">{{number_format($p->totalpotongan,0,",",".")}}</td>
                        </tr>
                        <br>
                        <tr align="right">
                            <td colspan="2"><strong><center>Diterima</center></strong></td>
                            <td align="right" style="padding-right:10">{{number_format($p->netto,0,",",".")}}</td>
                        </tr>
                        <tr>
                            <td colspan="3"><center>A : {{ $p->a }} I : {{ $p->i }} S : {{ $p->s }} ST : {{ $p->st }} C : {{ $p->c }}
                            TL1 : {{ $p->telat_kurang }} TL2 : {{ $p->telat_lebih }} STH : {{ $p->setengah_hari }} OFF : {{ $p->off }}</center></td>
                        </tr>
                    </table>
                </td>
                    @php $i++ @endphp
                    @if($i%2==0 && $i!==0)
                        </tr><tr>
                    @endif
            @endforeach
        </tr>
    </table>
</body>
</html>
