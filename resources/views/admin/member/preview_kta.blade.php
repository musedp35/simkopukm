@extends('admin.layouts')

@section('title', 'Preview KTA - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12 m8">
                    <h5 class="blue-text" style="font-size:1.5rem;">Preview KTA</h5>
                </div>
                <div class="col s12 m4" style="text-align:right;">
                    <a href="{{ route('admin.member.cetak_kta', $registry) }}" target="_blank" class="btn blue">
                        <i class="material-icons left">file_download</i> Unduh PDF
                    </a>
                    <button onclick="window.print()" class="btn orange" style="margin-left:8px;">
                        <i class="material-icons left">print</i> Cetak
                    </button>
                </div>
            </div>

            <div style="margin:1.5rem auto; max-width:600px; border:2px solid #0d47a1; position:relative; overflow:hidden;">
                <div style="background:#0d47a1;color:#fff;text-align:center;padding:8px 0;font-size:14px;font-weight:bold;letter-spacing:1px;">
                    KARTU TANDA ANGGOTA
                </div>
                <div style="display:flex;padding:16px;">
                    <table style="flex:1;font-size:12px;width:100%;">
                        <tr><td style="width:35%;padding:3px 0;color:#555;font-weight:bold;">No. Anggota</td><td><strong>{{ $kta['noAnggota'] ?? '-' }}</strong></td></tr>
                        <tr><td style="padding:3px 0;color:#555;font-weight:bold;">Nama</td><td><strong>{{ $kta['nama'] ?? '-' }}</strong></td></tr>
                        <tr><td style="padding:3px 0;color:#555;font-weight:bold;">Alamat</td><td>{{ $kta['alamat'] ?? '-' }}</td></tr>
                        <tr><td style="padding:3px 0;color:#555;font-weight:bold;">Tgl. Masuk</td><td><strong>{{ $kta['tglMasuk'] ?? '-' }}</strong></td></tr>
                        <tr><td style="padding:3px 0;color:#555;font-weight:bold;">No. Registrasi</td><td><strong>{{ $kta['noRegistry'] ?? '-' }}</strong></td></tr>
                    </table>
                    @if(!empty($kta['foto']))
                        <img src="{{ $kta['foto'] }}" alt="Foto" style="width:80px;height:100px;object-fit:cover;border:1px solid #000;margin-left:16px;">
                    @else
                        <div style="width:80px;height:100px;border:1px solid #999;display:flex;align-items:center;justify-content:center;color:#999;font-size:9px;margin-left:16px;background:#f5f5f5;">FOTO BELUM ADA</div>
                    @endif
                </div>
                <div style="text-align:right;padding:0 16px 12px;font-size:10px;color:#333;">
                    Dikeluarkan di Surabaya<br/>Pada: {{ $kta['tglCetak'] ?? date('d-m-Y') }}
                </div>
            </div>
        </div>
    </div>
@stop
