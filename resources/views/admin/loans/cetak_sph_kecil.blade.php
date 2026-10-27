<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPH — {{ $data['LOAN_ACCOUNT_NO'] ?? '' }}</title>
    <style>
        @page { size: A5; margin: 1cm; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #000; }
        .no-print { display: block; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        .kop { text-align: center; border-bottom: 1.5px solid #000; padding-bottom: 6px; margin-bottom: 10px; }
        .kop p { margin: 0; font-size: 11px; }
        .kop .nama-kop { font-weight: bold; font-size: 12px; }
        .judul { text-align: center; margin: 10px 0 4px; }
        .judul h3 { font-size: 14px; font-weight: bold; margin: 0; }
        .judul p  { font-size: 12px; font-weight: bold; margin: 2px 0; }
        .judul hr { width: 35%; border: none; border-top: 1.5px solid #000; margin: 0 auto; }
        .info-baris { margin-top: 4px; }
        table.sph-data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.sph-data td { padding: 2px 4px; vertical-align: top; }
        table.sph-data .label { width: 20%; font-weight: normal; }
        table.sph-data .colon  { width: 3%; }
        table.sph-data .value  { width: 77%; }
        .terbilang { font-style: italic; font-size: 10px; }
        .ttd { margin-top: 24px; display: flex; justify-content: space-between; }
        .ttd-box { text-align: center; width: 120px; }
        .ttd-line { height: 30px; border-top: 1px solid #000; margin-top: 2px; font-size: 10px; }
    </style>
</head>
<body>
<div class="no-print" style="margin-bottom:12px">
    <button onclick="window.print()" style="padding:6px 14px;background:#2563eb;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:13px">
        <i>🖨</i> Cetak
    </button>
    <a href="{{ url('admin/loan/laporan-sph') }}" style="margin-left:8px;font-size:13px;color:#2563eb;text-decoration:none">
        ← Kembali ke Laporan SPH
    </a>
</div>

<div style="max-width:420px;margin:0 auto">
    {{-- Kop --}}
    <div class="kop">
        <p class="nama-kop">KOPERASI KONSUMEN SETIA BHAKTI WANITA</p>
        <p>Jl. Jemur Andayani 55, Surabaya</p>
    </div>

    {{-- Judul --}}
    <div class="judul">
        <h3>SURAT PENGAKUAN HUTANG</h3>
        <hr>
        <p>No. {{ $data['SPH_NO'] ?? '-' }}</p>
    </div>

    {{-- Identitas --}}
    <p style="margin-top:6px">Yang bertanda tangan di bawah ini:</p>
    <table class="sph-data">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $data['MEMBER_NAME'] ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['ADDRESS_STAY'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Kredit</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $data['LOAN_ACCOUNT_NO'] ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">No. Anggota</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['MEMBER_NO'] ?? '-' }}</td>
        </tr>
    </table>

    {{-- Nominal --}}
    <p style="margin-top:8px">
        Mengakui telah menerima uang sebesar
        <strong>Rp {{ number_format($data['LOAN_AMOUNT'] ?? 0, 2) }}</strong>
    </p>
    <p class="terbilang">( nominal terbilang — isi manual saat cetak )</p>
    <p style="margin-top:4px">
        dari Koperasi Konsumen Setia Bhakti Wanita Surabaya
        (Badan Hukum No. 4362/BH/11/80 tanggal 15 Januari 1980)
        sebagai pinjaman yang akan kami bayar kembali dengan ketentuan sbb:
    </p>

    <table class="sph-data">
        <tr>
            <td class="label">Jangka Waktu</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['LOAN_PERIOD'] ?? '-' }} bulan</td>
        </tr>
        <tr>
            <td class="label">Jasa Pinjaman</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['INTEREST_SUBRATE'] ?? '-' }}% per bulan dari sisa pinjaman</td>
        </tr>
        <tr>
            <td class="label">Simpanan Org.</td>
            <td class="colon">:</td>
            <td class="value">{{ isset($data['INTEREST_SUBRATE']) ? (1 - $data['INTEREST_SUBRATE']) * 100 : '-' }}% per bulan</td>
        </tr>
        <tr>
            <td class="label">Agunan</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['LOAN_COLLATERAL_NAME'] ?? '-' }}</td>
        </tr>
    </table>

    {{-- TTD --}}
    <div class="ttd">
        <div class="ttd-box">
            <p style="margin-top:0">Dibuat di Surabaya,</p>
            <p>Tgl. {{ now()->format('d F Y') }}</p>
            <div class="ttd-line">Penerima / AO</div>
        </div>
        <div class="ttd-box">
            <p style="margin-top:0">Pemberi / Peminjam,</p>
            <div class="ttd-line">
                <br><br><br>
                <strong>{{ $data['MEMBER_NAME'] ?? '____________________' }}</strong>
            </div>
        </div>
    </div>
</div>
</body>
</html>
