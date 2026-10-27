<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPH Besar — {{ $data['LOAN_ACCOUNT_NO'] ?? '' }}</title>
    <style>
        @page { size: A4; margin: 2cm; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #000; }
        .no-print { display: block; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 16px; }
        .kop p { margin: 0; font-size: 13px; }
        .kop .nama-kop { font-weight: bold; font-size: 14px; }
        .judul { text-align: center; margin: 16px 0 8px; }
        .judul h2 { font-size: 16px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .judul hr { width: 40%; border: none; border-top: 2px solid #000; margin: 4px auto; }
        .judul p  { font-size: 14px; font-weight: bold; margin: 2px 0; }
        p { line-height: 1.6; }
        table.sph-data { width: 100%; border-collapse: collapse; margin: 8px 0; }
        table.sph-data td { padding: 4px 6px; vertical-align: top; }
        table.sph-data .label { width: 22%; }
        table.sph-data .colon  { width: 4%; }
        table.sph-data .value  { width: 74%; }
        .terbilang { font-style: italic; font-size: 11px; }
        .ttd { margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end; }
        .ttd-box { text-align: center; width: 160px; font-size: 11px; }
        .ttd-sign { height: 50px; }
        .ttd-line { border-top: 1px solid #000; padding-top: 3px; font-weight: bold; }
    </style>
</head>
<body>
<div class="no-print" style="margin-bottom:14px">
    <button onclick="window.print()" style="padding:8px 18px;background:#2563eb;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px">
        <i>🖨</i> Cetak A4
    </button>
    <a href="{{ url('admin/loan/laporan-sph') }}" style="margin-left:10px;font-size:14px;color:#2563eb;text-decoration:none">
        ← Kembali
    </a>
</div>

<div style="max-width:700px;margin:0 auto">
    {{-- Kop --}}
    <div class="kop">
        <p class="nama-kop">KOPERASI KONSUMEN SETIA BHAKTI WANITA</p>
        <p>Jl. Jemur Andayani 55, Surabaya — Badan Hukum No. 4362/BH/11/80</p>
    </div>

    {{-- Judul --}}
    <div class="judul">
        <h2>SURAT PENGAKUAN HUTANG</h2>
        <hr>
        <p>No. {{ $data['SPH_NO'] ?? '-' }}</p>
    </div>

    {{-- Isi --}}
    <p>
        Yang bertanda tangan di bawah ini:
    </p>

    <table class="sph-data">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $data['MEMBER_NAME'] ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['MEMBER_NIK'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['ADDRESS_STAY'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Anggota</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['MEMBER_NO'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Kredit</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $data['LOAN_ACCOUNT_NO'] ?? '-' }}</strong></td>
        </tr>
    </table>

    <p>
        Dengan ini mengakui telah menerima uang sebesar
        <strong>Rp {{ number_format($data['LOAN_AMOUNT'] ?? 0, 2) }}</strong>
    </p>
    <p class="terbilang">( nominal terbilang — isi manual saat cetak )</p>

    <p>
        dari Koperasi Konsumen Setia Bhakti Wanita Surabaya
        sebagai pinjaman yang akan kami bayar kembali dengan ketentuan sebagai berikut:
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
            <td class="value">
                {{ isset($data['INTEREST_SUBRATE'])
                    ? number_format((1 - $data['INTEREST_SUBRATE']) * 100, 2)
                    : '-' }}% per bulan
            </td>
        </tr>
        <tr>
            <td class="label">Provisi</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['PROVISION_RATE'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Agunan</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['LOAN_COLLATERAL_NAME'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Detail Agunan</td>
            <td class="colon">:</td>
            <td class="value">{{ $data['detailAgunan'] ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin-top:12px">
        Demikian surat pengakuan hutang ini dibuat dengan sebenarnya,
        dengan kesadaran sendiri dan tanpa adanya paksaan dari pihak mana pun.
    </p>

    {{-- TTD --}}
    <div class="ttd">
        <div class="ttd-box">
            Dibuat di Surabaya,
            Tgl. {{ now()->format('d F Y') }}
            <div class="ttd-sign"></div>
            Penerima / AO
            <div class="ttd-line">{{ $data['ACCOUNT_OFFICER_NAME'] ?? '______________________' }}</div>
        </div>
        <div class="ttd-box">
            Pemberi / Peminjam
            <div class="ttd-sign"></div>
            <div class="ttd-line">{{ $data['MEMBER_NAME'] ?? '______________________' }}</div>
        </div>
    </div>

    {{-- Tanda tangan Saksi (opsional) --}}
    <div class="ttd" style="margin-top:24px">
        <div class="ttd-box">
            Saksi 1
            <div class="ttd-sign"></div>
            <div class="ttd-line">______________________</div>
        </div>
        <div class="ttd-box">
            Saksi 2
            <div class="ttd-sign"></div>
            <div class="ttd-line">______________________</div>
        </div>
    </div>
</div>
</body>
</html>
