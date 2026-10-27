@extends('admin.layouts')

@section('title', 'Laporan Jumlah Anggota - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12 m8">
                    <h5 class="blue-text" style="font-size:1.5rem;">Laporan Jumlah Anggota (PD)</h5>
                </div>
                <div class="col s12 m4" style="text-align:right;">
                    <a href="{{ route('admin.member.lap_jumlah.export', $status) }}" class="btn blue">
                        <i class="material-icons left">file_download</i> Export Excel
                    </a>
                </div>
            </div>

            <div class="row" style="margin-top:1rem;">
                <div class="col s12">
                    <p class="grey-text" style="margin:0.5rem 0;">
                        Total: <strong class="blue-text">{{ $jumlah }}</strong> anggota
                    </p>
                </div>
            </div>

            {{-- Table Laporan --}}
            <div class="table-responsive" style="overflow-x:auto;">
                <table class="highlight striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Reg</th>
                            <th>No. Anggota</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $m)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $m->MEMBER_REGISTRY }}</td>
                                <td>{{ $m->MEMBER_NO }}</td>
                                <td>
                                    <a href="{{ route('admin.member.show', $m->MEMBER_REGISTRY) }}" class="blue-text">
                                        {{ $m->MEMBER_NAME }}
                                    </a>
                                </td>
                                <td>{{ $m->MEMBER_ADDRESS }}</td>
                                <td>{{ $m->MEMBER_DATE_REGISTRY ? \Carbon\Carbon::parse($m->MEMBER_DATE_REGISTRY)->format('d M Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center;padding:2rem;" class="grey-text">
                                Belum ada data anggota.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
