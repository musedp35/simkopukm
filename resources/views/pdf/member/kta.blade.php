<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>KTA — Kartu Tanda Anggota</title>
<style>
  body{font-family:Arial,Helvetica,sans-serif;font-size:10px;margin:0;background:#fff;}
  .kta{
    width:19cm; height:11cm;
    border:2px solid #003; position:relative;
    overflow:hidden; background:#fff; color:#000;
    font-size:10px;
  }
  .kta-header{
    position:absolute; top:0; left:0; right:0;
    background:#0d47a1; color:#fff; text-align:center;
    padding:6px 0; font-size:13px; font-weight:bold;
    letter-spacing:1px;
  }
  .kta-body{
    position:absolute; top:30px; left:0; right:0; bottom:0;
    display:flex; padding:14px 20px;
  }
  .kta-info{flex:1; display:flex; flex-direction:column; justify-content:center;}
  .kta-info table{width:100%; border:none; font-size:10px;}
  .kta-info td{padding:2px 0; vertical-align:top;}
  .kta-info td.label{width:30%; color:#555; font-weight:bold;}
  .kta-info td.value{width:70%; font-weight:bold; color:#000;}
  .kta-photo{
    width:90px; height:110px;
    border:1px solid #000; margin-left:20px;
    flex-shrink:0; object-fit:cover;
  }
  .kta-photo-placeholder{
    width:90px; height:110px; border:1px solid #999;
    display:flex; align-items:center; justify-content:center;
    color:#999; font-size:8px; margin-left:20px;
    flex-shrink:0; background:#f5f5f5;
  }
  .kta-sign{
    position:absolute; bottom:8px; right:20px; text-align:right;
    font-size:8px;
  }
</style></head><body>
<div class="kta">
  <div class="kta-header">KARTU TANDA ANGGOTA</div>
  <div class="kta-body">
    <div class="kta-info">
      <table>
        <tr><td class="label">No. Anggota</td><td class="value">{{ $kta['noAnggota'] ?? '-' }}</td></tr>
        <tr><td class="label">Nama</td><td class="value">{{ $kta['nama'] ?? '-' }}</td></tr>
        <tr><td class="label">Alamat</td><td class="value">{{ $kta['alamat'] ?? '-' }}</td></tr>
        <tr><td class="label">Tgl. Masuk</td><td class="value">{{ $kta['tglMasuk'] ?? '-' }}</td></tr>
        <tr><td class="label">No. Registrasi</td><td class="value">{{ $kta['noRegistry'] ?? '-' }}</td></tr>
      </table>
    </div>
    @if(!empty($kta['foto']))
      <img class="kta-photo" src="{{ $kta['foto'] }}" alt="Foto Anggota">
    @else
      <div class="kta-photo-placeholder">FOTO<br/>BELUM<br/>ADA</div>
    @endif
    <div class="kta-sign">
      Dikeluarkan di Surabaya<br/>
      Pada: {{ $kta['tglCetak'] ?? date('d-m-Y') }}
    </div>
  </div>
</div>
</body></html>
