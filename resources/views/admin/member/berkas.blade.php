@extends('admin.layouts')

@section('title', 'Berkas Anggota - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row" style="margin-bottom:1rem;">
                <div class="col s12 m8">
                    <h5 style="font-size:1.3rem; color:#1565c0;">Berkas Anggota</h5>
                    <p class="grey-text" style="font-size:0.85rem;">
                        No. Reg: <strong>{{ $member->MEMBER_REGISTRY }}</strong> &nbsp;|&nbsp;
                        Nama: <strong>{{ $member->MEMBER_NAME }}</strong> &nbsp;|&nbsp;
                        No. Anggota: <strong>{{ $member->MEMBER_NO }}</strong>
                    </p>
                </div>
                <div class="col s12 m4" style="text-align:right;">
                    <a href="{{ route('admin.member.show', $registry) }}" class="btn-small blue">Kembali ke Detail</a>
                </div>
            </div>

            {{-- Status Berkas --}}
            <div class="row" style="margin-bottom:1rem;">
                <div class="col s12">
                    <table class="highlight" style="max-width:400px;">
                        <thead>
                            <tr>
                                <th>Jenis Berkas</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($berkasStatus as $type => $status)
                            <tr>
                                <td>{{ strtoupper($type) }}</td>
                                <td>
                                    @if($status['code'] === '1')
                                        <span class="chip green lighten-2">Sudah Ada</span>
                                    @else
                                        <span class="chip grey lighten-2">Belum Ada</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form Upload --}}
            <form action="{{ route('admin.member.berkas.proses', $registry) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="row">
                @csrf

                <div class="col s12">
                    <h6 style="font-size:1rem; font-weight:600; margin-bottom:0.8rem;">Unggah / Ganti Berkas</h6>
                    <p class="grey-text" style="font-size:0.8rem; margin-bottom:1rem;">
                        Format: JPG, PNG, atau GIF — Maks. 2 MB per file. Kosongkan jika tidak ingin mengubah.
                    </p>

                    <div class="row">
                        @php
                            $fields = [
                                'images_1' => ['label' => 'KTP (Sisi Depan)', 'type' => 'KTP'],
                                'images_2' => ['label' => 'Pas Foto',           'type' => 'PHOTO'],
                                'images_3' => ['label' => 'Tanda Tangan',       'type' => 'TTD'],
                                'images_4' => ['label' => 'Kartu Keluarga',    'type' => 'KK'],
                            ];
                        @endphp
                        @foreach($fields as $field => $meta)
                        <div class="col s12 m6">
                            <div class="file-field input-field" style="margin-bottom:1rem;">
                                <div class="btn-flat blue lighten-2" style="border:1px solid #90a4ae; border-radius:4px; font-size:0.8rem; text-transform:uppercase;">
                                    <input type="file"
                                           name="{{ $field }}"
                                           accept=".jpg,.jpeg,.png,.gif">
                                    <span>{{ $meta['label'] }}</span>
                                </div>
                                <p class="grey-text" style="font-size:0.75rem; margin-top:2px;">
                                    ({{ $meta['type'] }}) — {{ $berkasStatus[$meta['type']]['code'] === '1' ? 'sudah ada, unggah untuk ganti' : 'belum ada' }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row" style="margin-top:1rem;">
                        <div class="col s12">
                            <button type="submit" class="btn blue waves-effect" style="text-transform:uppercase; font-size:0.8rem;">
                                Simpan Berkas
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Preview Gambar yang Sudah Ada --}}
            @if($berkas->count() > 0)
            <div class="row" style="margin-top:1.5rem;">
                <div class="col s12">
                    <h6 style="font-size:1rem; font-weight:600; margin-bottom:0.8rem;">Berkas yang Tersimpan</h6>
                    <div class="row">
                        @foreach($berkas as $img)
                        @if($img->IMAGE_FILE !== '')
                        <div class="col s6 m3">
                            <div class="card" style="padding:8px; margin:0;">
                                <div class="card-content" style="padding:8px;">
                                    <p class="center grey-text" style="font-size:0.8rem; font-weight:600;">{{ strtoupper($img->IMAGE_TYPE) }}</p>
                                    @php
                                        // IMAGE_FILE disimpan sebagai base64 string (tanpa prefix data URI)
                                        $mime = 'image/jpeg';
                                        if (str_contains($img->IMAGE_FILE, 'iVBOR')) $mime = 'image/png';
                                        elseif (str_contains($img->IMAGE_FILE, 'R0lG')) $mime = 'image/gif';
                                        $src = "data:{$mime};base64,{$img->IMAGE_FILE}";
                                    @endphp
                                    <img src="{{ $src }}"
                                         alt="{{ strtoupper($img->IMAGE_TYPE) }}"
                                         style="width:100%; height:120px; object-fit:cover; border-radius:4px;">
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
@stop
