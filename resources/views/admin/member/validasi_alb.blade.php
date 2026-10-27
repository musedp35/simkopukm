@extends('admin.layouts')

@section('title', 'Validasi Anggota ALB - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <h5 class="blue-text" style="font-size:1.5rem;">Validasi Anggota ALB</h5>
                    <a href="{{ route('admin.member.index') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
                        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali ke daftar
                    </a>
                </div>
            </div>

            {{-- Data Anggota --}}
            <div class="row">
                <div class="col s12">
                    <table style="width:100%; border:0;">
                        <tbody>
                            <tr><td style="width:180px;padding:8px 0;color:#666;">No. Member</td><td style="padding:8px 0;"><strong>{{ $member->MEMBER_NO }}</strong></td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Nama</td><td style="padding:8px 0;"><strong>{{ $member->MEMBER_NAME }}</strong></td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Email</td><td style="padding:8px 0;">{{ $member->MEMBER_EMAIL ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">No. Rekening</td><td style="padding:8px 0;">{{ $member->BANKING_ACCOUNT_NO ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Alamat</td><td style="padding:8px 0;">{{ $member->ADDRESS_STAY ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Kelurahan</td><td style="padding:8px 0;">{{ $member->village->VILLAGE_NAME ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Kecamatan</td><td style="padding:8px 0;">{{ $member->district->DISTRICT_NAME ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Kota</td><td style="padding:8px 0;">{{ $member->city->CITY_NAME ?? '-' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Berkas Anggota (KTP, KK, TTD, FOTO) --}}
            <div class="row">
                <div class="col s12">
                    <h6 class="blue-text" style="font-size:1.1rem;">Berkas Anggota</h6>
                    <div class="row">
                        @foreach(['KTP', 'KK', 'TTD', 'PHOTO'] as $jenis)
                            <div class="col s6 m3">
                                <div class="card-panel" style="min-height:120px;">
                                    <p style="font-size:0.85rem;color:#666;">{{ $jenis }}</p>
                                    @if(!empty($berkas[$jenis]->IMAGE_FILE))
                                        <img src="{{ asset('uploads/images/'.$berkas[$jenis]->IMAGE_FILE) }}" alt="{{ $jenis }}"
                                             style="max-width:100%;max-height:150px;object-fit:cover;">
                                    @else
                                        <p class="grey-text" style="margin-top:1rem;">Belum diunggah</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tombol Proses Validasi --}}
            <div class="row">
                <div class="col s12">
                    <form method="POST" action="{{ route('admin.member.validasi.proses', ['type' => $type, 'registry' => $registry]) }}"
                          onsubmit="return confirm('Validasi anggota ini sebagai ALB? Status akan berubah menjadi NTR.');">
                        @csrf
                        <button type="submit" class="btn green">
                            <i class="material-icons left">check</i> Proses Validasi ALB
                        </button>
                        <a href="{{ route('admin.member.show', $registry) }}" class="btn grey" style="margin-left:8px;">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
