@extends('admin.layouts')
@section('title', 'Cetak BKM Pinjaman')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Bukti Kas Masuk — Pinjaman</h4>
    <div class="card">
        <div class="card-content">
            @if($row)
                <table class="striped">
                    <tr><td>No. Anggota</td><td>{{ $row->MEMBER_NO ?? '-' }}</td></tr>
                    <tr><td>Nama</td><td>{{ $row->MEMBER_NAME ?? '-' }}</td></tr>
                    <tr><td>Tanggal</td><td>{{ $row->TRANSACTION_DATE ?? '-' }}</td></tr>
                    <tr><td>Jumlah</td><td>{{ number_format($row->AMOUNT ?? 0, 2) }}</td></tr>
                    <tr><td>Keterangan</td><td>{{ $row->REMARKS ?? 'Pinjaman' }}</td></tr>
                </table>
                <button onclick="window.print()" class="btn waves-effect waves-light blue">Cetak</button>
            @else
                <p>Belum ada data untuk dicetak.</p>
            @endif
        </div>
    </div>
</div>
@endsection
