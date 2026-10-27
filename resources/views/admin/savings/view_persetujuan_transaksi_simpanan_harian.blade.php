@extends('admin.layouts')
@section('title', 'Persetujuan Transaksi Simpanan Harian - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Persetujuan Transaksi Simpanan Harian</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif

    <div class="table-responsive">
        <table class="highlight">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Transaksi</th>
                    <th>Tgl Transaksi</th>
                    <th>No. Akun</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    @if(is_array($row))
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['TRANSACTION_NO'] ?? $row['TRANSACTION_ID'] ?? '-' }}</td>
                        <td>{{ $row['TRANSACTION_DATE'] ?? '-' }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $row['TRANSACTION_TYPE'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['AMOUNT'] ?? 0), 2) }}</td>
                        <td>
                            @if((int)($row['APPROVAL_STATUS_ID'] ?? 0) === 1)
                                <span class="chip green white-text">Approved</span>
                            @else
                                <span class="chip orange white-text">Menunggu</span>
                            @endif
                        </td>
                        <td>
                            <div class="row">
                                <div class="col s6">
                                    <form method="POST" action="{{ route('admin.savings.proses_approval') }}">
                                        @csrf
                                        <input type="hidden" name="idTrans" value="{{ $row['TRANSACTION_ID'] ?? '' }}">
                                        <input type="radio" name="chbApproval" value="1" required> <label style="font-size:0.9rem;">Approve</label>
                                        <input type="radio" name="chbApproval" value="0"> <label style="font-size:0.9rem;">Tolak</label>
                                        <button type="submit" class="btn small blue waves-effect"><i class="material-icons left">check</i> Proses</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="10" class="center grey-text">Belum ada transaksi untuk disetujui</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
