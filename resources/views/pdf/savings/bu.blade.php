<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>BU</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;font-size:11px;margin:20px;}
table{width:100%;border-collapse:collapse;} th,td{border:1px solid #000;padding:6px 8px;text-align:left;} th{background:#eee;} .right{text-align:right;}
.head{text-align:center;margin-bottom:14px;} .head h1{margin:0;font-size:18px;} .head p{margin:2px 0;}
.sign{margin-top:40px;width:100%;} .sign td{border:none;text-align:center;}
</style></head><body>
<div class="head"><h1>SIMPANAN — BUKTI URUTAN (BU)</h1><p>SIMKOP UKM</p></div>
<table><tbody>
<tr><th>No. Transaksi</th><td>{{ $row['TRANSACTION_NO'] ?? '-' }}</td></tr>
<tr><th>Tanggal</th><td>{{ $row['TRANSACTION_DATE'] ?? '-' }}</td></tr>
<tr><th>Nama</th><td>{{ $row['MEMBER_NAME'] ?? '-' }}</td></tr>
<tr><th>No. Akun</th><td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td></tr>
<tr><th>Jenis</th><td>{{ $row['TRANSACTION_TYPE'] ?? '-' }}</td></tr>
<tr><th>Jumlah</th><td class="right">Rp {{ number_format((float)($row['AMOUNT'] ?? 0),2,',','.') }}</td></tr>
<tr><th>Status</th><td>{{ $row['APPROVAL_STATUS_ID'] == 1 ? 'Disetujui' : ($row['APPROVAL_STATUS_ID'] == 0 ? 'Menunggu' : 'Ditolak') }}</td></tr>
</tbody></table>
<table class="sign"><tr><td>Diketik oleh</td><td>Disahkan oleh</td><td>Ketua Kasie</td></tr>
<tr><td colspan="3">&nbsp;</td></tr><tr><td>( ____________________ )</td><td>( ____________________ )</td><td>( ____________________ )</td></tr></table>
</body></html>
