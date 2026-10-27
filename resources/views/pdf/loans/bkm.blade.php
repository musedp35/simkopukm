<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>BKM Pinjaman</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;font-size:11px;margin:20px;}
h2{margin:0 0 4px;} .sub{color:#555;margin-bottom:16px;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid #000;padding:6px 8px;text-align:left;}
th{background:#eee;} .right{text-align:right;}
.head{text-align:center;margin-bottom:14px;} .head h1{margin:0;font-size:18px;} .head p{margin:2px 0;}
.sign{margin-top:40px;width:100%;} .sign td{border:none;text-align:center;}
</style></head><body>
<div class="head"><h1>PINJAMAN — BUKTI KAS MASUK (BKM)</h1><p>PT Koperasi Simkop UKM</p></div>
<table><tbody>
<tr><th>No. Transaksi</th><td>{{ $row['TRANS_ID'] ?? '-' }}</td></tr>
<tr><th>Tanggal</th><td>{{ $row['TRANSACTION_DATE'] ?? '-' }}</td></tr>
<tr><th>Nama Penerima</th><td>{{ $row['MEMBER_NAME'] ?? '-' }}</td></tr>
<tr><th>No. Kredit</th><td>{{ $row['LOAN_ACCOUNT_NO'] ?? '-' }}</td></tr>
<tr><th>Keterangan</th><td>Realisasi Pinjaman</td></tr>
<tr><th>Nominal</th><td class="right">Rp {{ number_format((float)($row['AMOUNT'] ?? 0),2,',','.') }}</td></tr>
</tbody></table>
<table class="sign"><tr><td>Diketik oleh</td><td>Diterima oleh</td><td>Ketua Kasie</td></tr>
<tr><td colspan="3">&nbsp;</td></tr><tr><td>( ____________________ )</td><td>( ____________________ )</td><td>( ____________________ )</td></tr></table>
</body></html>