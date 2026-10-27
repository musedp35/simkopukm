@extends('admin.layouts')

@section('title', 'Daftar Member - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <div class="row">
                        <div class="col s12 m8">
                            <h5 class="blue-text" style="font-size: 1.5rem;">Daftar Member</h5>
                        </div>
                        <div class="col s12 m4" style="text-align: right;">
                            <a href="{{ route('admin.member.create') }}" class="btn blue">
                                <i class="material-icons left">add</i> Tambah Member
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search & Filter --}}
            <form class="row" method="GET" action="{{ route('admin.member.index') }}">
                <div class="col s12 m4">
                    <div class="input-field">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari no / nama / email">
                        <label>Cari Member</label>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="input-field">
                        <select name="status">
                            <option value="">-- Semua Status --</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->MEMBER_STATUS_ID }}" {{ $filterStatus == $st->MEMBER_STATUS_ID ? 'selected' : '' }}>
                                    {{ $st->MEMBER_STATUS_NAME }}
                                </option>
                            @endforeach
                        </select>
                        <label>Status</label>
                    </div>
                </div>
                <div class="col s12 m4">
                    <button class="btn blue" type="submit" style="width: 100%;">
                        <i class="material-icons left">search</i> Filter
                    </button>
                </div>
            </form>
            <script>document.querySelectorAll('select').forEach(s => M.FormSelect.instance(s) || M.FormSelect.init(s));</script>

            {{-- Table --}}
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="highlight striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Member Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Daftar Sejak</th>
                            <th>No. Rekening</th>
                            <th>Status Aktif</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $m)
                            <tr>
                                <td>{{ $m->MEMBER_NO }}</td>
                                <td>
                                    <a href="{{ route('admin.member.show', $m->MEMBER_REGISTRY) }}" class="blue-text">
                                        {{ $m->MEMBER_NAME }}
                                    </a>
                                </td>
                                <td>{{ $m->MEMBER_EMAIL ?? '-' }}</td>
                                <td>
                                    <span class="chip {{ $m->MEMBER_STATUS_NAME ? 'blue lighten-2' : 'grey lighten-2' }}">
                                        {{ $m->MEMBER_STATUS_NAME ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $m->MEMBER_DATE_REGISTRY ? \Carbon\Carbon::parse($m->MEMBER_DATE_REGISTRY)->format('d M Y') : '-' }}</td>
                                <td>{{ $m->BANKING_ACCOUNT_NO ?? '-' }}</td>
                                <td>
                                    @if($m->ACTIVE_STATUS_ID == 1)
                                        <span class="chip green lighten-2">Aktif</span>
                                    @else
                                        <span class="chip red lighten-2">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 4px;">
                                        <a href="{{ route('admin.member.show', $m->MEMBER_REGISTRY) }}" class="btn-small blue" title="Detail">
                                            <i class="material-icons" style="font-size: 16px;">visibility</i>
                                        </a>
                                        <a href="{{ route('admin.member.edit', $m->MEMBER_REGISTRY) }}" class="btn-small orange" title="Edit">
                                            <i class="material-icons" style="font-size: 16px;">edit</i>
                                        </a>
                                        @if($m->ACTIVE_STATUS_ID == 1)
                                        <form action="{{ route('admin.member.destroy', $m->MEMBER_REGISTRY) }}" method="POST"
                                              style="margin: 0;"
                                              onsubmit="return confirm('Matikan member ini?');">
                                            @csrf @method('DELETE')
                                            <button class="btn-small red" title="Hapus" type="submit">
                                                <i class="material-icons" style="font-size: 16px;">delete</i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" style="text-align:center; padding: 2rem;" class="grey-text">
                                Belum ada data member.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div style="margin-top: 1rem;">
                {{ $members->appends(['search' => $search, 'status' => $filterStatus])->links() }}
            </div>
        </div>
    </div>
@stop
