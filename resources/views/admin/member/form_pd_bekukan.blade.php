@extends('admin.layouts')

@section('title', ($action == 'pd' ? 'Proses PD (Pengunduran Diri)' : 'Proses Pembekuan') . ' - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <h5 class="blue-text" style="font-size:1.5rem;">
                        @if($action == 'pd')
                            Proses PD (Pengunduran Diri)
                        @else
                            Proses Pembekuan Anggota
                        @endif
                    </h5>
                    <a href="{{ route('admin.member.show', $registry) }}" class="grey-text" style="display:block;margin-bottom:1rem;">
                        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali ke detail
                    </a>
                </div>
            </div>

            {{-- Data anggota yang akan di-PD / dibekukan --}}
            <div class="row">
                <div class="col s12">
                    <table style="width:100%; border:0;">
                        <tbody>
                            <tr><td style="width:180px;padding:8px 0;color:#666;">No. Member</td><td style="padding:8px 0;"><strong>{{ $member->MEMBER_NO }}</strong></td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Nama</td><td style="padding:8px 0;"><strong>{{ $member->MEMBER_NAME }}</strong></td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Email</td><td style="padding:8px 0;">{{ $member->MEMBER_EMAIL ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">No. Rekening</td><td style="padding:8px 0;">{{ $member->BANKING_ACCOUNT_NO ?? '-' }}</td></tr>
                            <tr><td style="padding:8px 0;color:#666;">Status Aktif</td><td style="padding:8px 0;">
                                @if($member->ACTIVE_STATUS_ID == 1)
                                    <span class="chip green lighten-2">Aktif</span>
                                @elseif($member->ACTIVE_STATUS_ID == 2)
                                    <span class="chip orange lighten-2">Dibekukan</span>
                                @elseif($member->ACTIVE_STATUS_ID == 3)
                                    <span class="chip red lighten-2">PD / Mengundurkan Diri</span>
                                @else
                                    <span class="chip grey lighten-2">Non-Aktif</span>
                                @endif
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form input alasan --}}
            <div class="row">
                <div class="col s12">
                    <form method="POST" action="{{ $action == 'pd' ? route('admin.member.pd.proses', $registry) : route('admin.member.bekukan.proses', $registry) }}">
                        @csrf

                        @if($action == 'pd')
                            {{-- Pilih alasan PD (sama seperti CI3 form_pd_anggota) --}}
                            <div class="row">
                                <div class="col s12 m6">
                                    <div class="input-field">
                                        <select name="alasanPD" class="browser-default">
                                            <option value="" disabled selected> -- Pilih Alasan PD -- </option>
                                            @foreach($opsiAlasanPD as $val => $label)
                                                <option value="{{ $val }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <label>Alasan Pengunduran Diri</label>
                                    </div>
                                </div>
                                <div class="col s12 m6">
                                    <div class="input-field">
                                        <select name="jnsClose" class="browser-default">
                                            <option value="" disabled selected> -- Pilih Jenis Close -- </option>
                                            @foreach($opsiJnsClose as $val => $label)
                                                <option value="{{ $val }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <label>Jenis Penutupan</label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col s12">
                                <div class="input-field">
                                    <textarea name="areaAlasan" class="materialize-textarea" rows="3"></textarea>
                                    <label>Detail / Keterangan Alasan</label>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top:1.5rem;">
                            <div class="col s12">
                                <button type="submit" class="btn red"
                                        onsubmit="return confirm('Apakah Anda yakin ingin memproses {{ $action == 'pd' ? 'PD (Pengunduran Diri)' : 'Pembekuan' }} untuk anggota ini?');">
                                    <i class="material-icons left">warning</i>
                                    @if($action == 'pd')
                                        Proses PD (Pengunduran Diri)
                                    @else
                                        Proses Pembekuan
                                    @endif
                                </button>
                                <a href="{{ route('admin.member.show', $registry) }}" class="btn grey" style="margin-left:8px;">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('admin-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select').forEach(sel => M.FormSelect.init(sel));
            document.querySelectorAll('textarea').forEach(ta => M.Textarea.init(ta));
        });
    </script>
@stop
