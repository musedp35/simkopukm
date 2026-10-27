@extends('admin.layouts')
@section('title', 'Menu Kasir - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Menu Kasir — Transaksi Hari Ini ({{ $tgl ?? '—' }})</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    <div class="row">
        <div class="col s6"><h6 class="green-text">Setoran ({{ count($SETOR_HARI_INI ?? []) }})</h6>
            <div class="table-responsive"><table class="highlight"><thead><tr><th>No</th><th>Akun</th><th>Nama</th><th>Nominal</th><th>Tgl</th></tr></thead><tbody>
                @forelse($SETOR_HARI_INI ?? [] as $r)
                    <tr><td>{{ $r['TRANSACTION_ID'] }}</td><td>{{ $r['SAVINGS_ACCOUNT_NO'] }}</td><td>{{ $r['MEMBER_NAME'] }}</td><td>Rp {{ number_format($r['AMOUNT'] ?? 0,2,',','.') }}</td><td>{{ $r['TRANSACTION_DATE'] }}</td></tr>
                @empty <tr><td colspan="5" class="center grey-text">Tidak ada setoran hari ini.</td></tr> @endforelse
            </tbody></table></div>
        </div>
        <div class="col s6"><h6 class="red-text">Penarikan ({{ count($TARIK_HARI_INI ?? []) }})</h6>
            <div class="table-responsive"><table class="highlight"><thead><tr><th>No</th><th>Akun</th><th>Nama</th><th>Nominal</th><th>Tgl</th></tr></thead><tbody>
                @forelse($TARIK_HARI_INI ?? [] as $r)
                    <tr><td>{{ $r['TRANSACTION_ID'] }}</td><td>{{ $r['SAVINGS_ACCOUNT_NO'] }}</td><td>{{ $r['MEMBER_NAME'] }}</td><td>Rp {{ number_format($r['AMOUNT'] ?? 0,2,',','.') }}</td><td>{{ $r['TRANSACTION_DATE'] }}</td></tr>
                @empty <tr><td colspan="5" class="center grey-text">Tidak ada penarikan hari ini.</td></tr> @endforelse
            </tbody></table></div>
        </div>
    </div>
</div></div>
@endsection
