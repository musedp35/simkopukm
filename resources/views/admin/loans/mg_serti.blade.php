@extends('admin.layouts')
@section('title', 'Detail MG Serti — '.e($no))

@section('admin-content')
<div class="container">
    <div class="row" style="margin-bottom:1rem">
        <h4 class="page-title">Detail MG Serti <span class="text-muted">({{ $no }})</span></h4>
        <a href="{{ route('admin.loan.pj_pinjaman') }}" class="btn btn-sm btn-secondary">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Info Akun --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-file-invoice-dollar"></i> Info Akun</span></div>
        <div class="card-content">
            @if($account)
                <div class="row">
                    <div class="col s12 m6 l4">
                        <label class="form-label">No. Anggota</label>
                        <p class="text-value">{{ $account->MEMBER_NO ?? '-' }}</p>
                    </div>
                    <div class="col s12 m6 l4">
                        <label class="form-label">Nama</label>
                        <p class="text-value">{{ $account->MEMBER_NAME ?? '-' }}</p>
                    </div>
                    <div class="col s12 m6 l4">
                        <label class="form-label">No. Kredit</label>
                        <p class="text-value">{{ $no }}</p>
                    </div>
                    <div class="col s12 m6 l4">
                        <label class="form-label">Jumlah Pinjaman</label>
                        <p class="text-value">Rp {{ number_format($account->LOAN_AMOUNT ?? 0, 2) }}</p>
                    </div>
                    <div class="col s12 m6 l4">
                        <label class="form-label">Sisa</label>
                        <p class="text-value text-danger">Rp {{ number_format($account->REMAINING_AMOUNT ?? 0, 2) }}</p>
                    </div>
                    <div class="col s12 m6 l4">
                        <label class="form-label">Status</label>
                        <p class="text-value">{{ $account->LOAN_STATUS_ID ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="text-muted">Akun <code>{{ $no }}</code> tidak ditemukan.</p>
            @endif
        </div>
    </div>

    {{-- Riwayat Angsuran --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-history"></i> Riwayat Angsuran</span></div>
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Angs.</th>
                        <th>Tgl Jatuh Tempo</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($installments as $i)
                    <tr>
                        <td>{{ $i->INSTALLMENT_NO ?? '-' }}</td>
                        <td>{{ $i->DUE_DATE ? date('d/m/Y', strtotime($i->DUE_DATE)) : '-' }}</td>
                        <td>Rp {{ number_format($i->AMOUNT ?? 0, 2) }}</td>
                        <td>
                            @php
                                $s = (int)($i->STATUS_ID ?? 0);
                                $label = match($s) {
                                    0 => 'Belum Bayar',
                                    1 => 'Lunas',
                                    7 => 'Jatuh Tempo',
                                    default => "Status {$s}",
                                };
                                $color = $s === 1 ? 'green-text' : ($s === 7 ? 'red-text' : 'text-muted');
                            @endphp
                            <span class="{{ $color }}">{{ $label }}</span>
                        </td>
                        <td>{{ $i->PAID_DATE ? date('d/m/Y H:i', strtotime($i->PAID_DATE)) : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center text-muted">Belum ada data angsuran.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tautan --}}
    <div class="card">
        <div class="card-header"><span>Aksi</span></div>
        <div class="card-content">
            <div class="row">
                <div class="col s12 m4 l3">
                    <a href="{{ route('admin.loan.form_survey', ['no' => $no]) }}" class="btn btn-outline">
                        <i class="fas fa-clipboard-list"></i> Survey
                    </a>
                </div>
                <div class="col s12 m4 l3">
                    <a href="{{ route('admin.loan.pelunasan', ['no' => $no]) }}" class="btn btn-outline">
                        <i class="fas fa-check-double"></i> Pelunasan
                    </a>
                </div>
                <div class="col s12 m4 l3">
                    <a href="{{ route('admin.loan.agunan', ['no' => $no]) }}" class="btn btn-outline">
                        <i class="fas fa-handshake"></i> Agunan
                    </a>
                </div>
                <div class="col s12 m4 l3">
                    <a href="{{ route('admin.loan.log_ao', ['no' => $no]) }}" class="btn btn-outline">
                        <i class="fas fa-list"></i> Log AO/PJ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
