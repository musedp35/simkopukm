@extends('admin.layouts')

@section('title', 'Detail Member - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <h5 class="blue-text" style="font-size:1.5rem;">Detail Member</h5>
                    <a href="{{ route('admin.member.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
                        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali ke daftar
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <table style="width:100%;" border="0">
                        <tbody>
                            <tr><td style="width:180px;padding:8px 0;color:#666;">No. Member</td><td style="padding:8px 0;"><strong>{{ $member->MEMBER_NO }}</strong></td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Nama</td><td style="padding:8px 0;"><strong>{{ $member->MEMBER_NAME }}</strong></td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Email</td><td style="padding:8px 0;">{{ $member->MEMBER_EMAIL ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">No. Rekening</td><td style="padding:8px 0;">{{ $member->BANKING_ACCOUNT_NO ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Alamat Tinggal</td><td style="padding:8px 0;">{{ $member->ADDRESS_STAY ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Desa / Kelurahan</td><td style="padding:8px 0;">{{ $member->village->VILLAGE_NAME ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Kecamatan</td><td style="padding:8px 0;">{{ $member->district->DISTRICT_NAME ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Kota / Kab</td><td style="padding:8px 0;">{{ $member->city->CITY_NAME ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Status Member</td><td style="padding:8px 0;"><span class="chip blue lighten-2">{{ $statuses->firstWhere('MEMBER_STATUS_ID', $member->MEMBER_STATUS_ID)?->MEMBER_STATUS_NAME ?? '-' }}</span></td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Status</td><td style="padding:8px 0;">@if($member->ACTIVE_STATUS_ID == 1)<span class="chip green lighten-2">Aktif</span>@else<span class="chip red lighten-2">Non-Aktif</span>@endif</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Daftar Sejak</td><td style="padding:8px 0;">{{ $member->MEMBER_DATE_REGISTRY ? \Carbon\Carbon::parse($member->MEMBER_DATE_REGISTRY)->format('d M Y H:i') : '-' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <a href="{{ route('admin.member.edit', $member->MEMBER_REGISTRY) }}" class="btn orange"><i class="material-icons left">edit</i> Edit Member</a>
                    @if($member->ACTIVE_STATUS_ID == 1)
                        <a href="{{ route('admin.member.form', ['action' => 'pd', 'registry' => $member->MEMBER_REGISTRY]) }}" class="btn red lighten-1" style="margin-left:8px;"><i class="material-icons left">warning</i> Proses PD</a>
                        <a href="{{ route('admin.member.form', ['action' => 'bekukan', 'registry' => $member->MEMBER_REGISTRY]) }}" class="btn amber" style="margin-left:8px;"><i class="material-icons left">block</i> Bekukan</a>
                    @endif
                    <a href="{{ route('admin.member.preview_kta', $member->MEMBER_REGISTRY) }}" class="btn teal" style="margin-left:8px;"><i class="material-icons left">credit_card</i> Preview KTA</a>
                    <a href="{{ route('admin.member.berkas', $member->MEMBER_REGISTRY) }}" class="btn deep-orange" style="margin-left:8px;"><i class="material-icons left">attach_file</i> Berkas</a>
                </div>
            </div>

            {{-- Savings Accounts --}}
            <div class="row" style="margin-top:1.5rem;">
                <div class="col s12">
                    <h6 class="blue-text" style="font-size:1.1rem;">Akun Simpanan</h6>
                    @if($member->savings->count() > 0)
                        <table class="highlight striped" style="width:100%;">
                            <thead><tr><th>No. Akun</th><th>Saldo</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($member->savings as $sav)
                                    <tr>
                                        <td>{{ $sav->SAVINGS_ACCOUNT_NO }}</td>
                                        <td>Rp {{ number_format($sav->SAVINGS_BALANCE, 2, ',', '.') }}</td>
                                        <td>@if($sav->ACTIVE_ACCOUNT_ID == 1)<span class="chip green lighten-2">Aktif</span>@else<span class="chip grey lighten-2">Non-Aktif</span>@endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="grey-text">Belum ada akun simpanan.</p>
                    @endif
                </div>
            </div>

            {{-- Loans --}}
            <div class="row">
                <div class="col s12">
                    <h6 class="blue-text" style="font-size:1.1rem;">Pinjaman</h6>
                    @if($member->loans->count() > 0)
                        <table class="highlight striped" style="width:100%;">
                            <thead><tr><th>No. Pinjaman</th><th>Jumlah</th><th>Sisa</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($member->loans as $loan)
                                    <tr>
                                        <td>{{ $loan->LOAN_ACCOUNT_NO }}</td>
                                        <td>Rp {{ number_format($loan->LOAN_AMOUNT, 2, ',', '.') }}</td>
                                        <td>Rp {{ number_format($loan->REMAINING_AMOUNT, 2, ',', '.') }}</td>
                                        <td><span class="chip {{ $loan->LOAN_STATUS_ID == 1 ? 'green lighten-2' : 'orange lighten-2' }}">{{ $loan->LOAN_STATUS_ID == 1 ? 'Aktif' : 'Selesai' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="grey-text">Belum ada pinjaman.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
