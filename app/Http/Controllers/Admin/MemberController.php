<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Member;
use App\Models\Village;
use App\Models\District;
use App\Models\City;
use App\Models\TypeCity;
use App\Models\MemberStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\ExcelService;
use App\Services\PdfService;
use App\Models\Image;

class MemberController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $filterStatus = $request->input('status', '');

        $query = DB::table('ma_member as m')
            ->leftJoin('ma_member_status as mms', 'mms.MEMBER_STATUS_ID', '=', 'm.MEMBER_STATUS_ID')
            ->select(
                'm.MEMBER_REGISTRY', 'm.MEMBER_NO', 'm.MEMBER_NAME',
                'm.MEMBER_EMAIL', 'm.MEMBER_STATUS_ID',
                'mms.MEMBER_STATUS_NAME', 'm.ACTIVE_STATUS_ID',
                'm.MEMBER_DATE_REGISTRY', 'm.BANKING_ACCOUNT_NO'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('m.MEMBER_NO', 'like', "%{$search}%")
                  ->orWhere('m.MEMBER_NAME', 'like', "%{$search}%")
                  ->orWhere('m.MEMBER_EMAIL', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $query->where('m.MEMBER_STATUS_ID', $filterStatus);
        }

        $members = $query->orderBy('m.MEMBER_NO', 'asc')->paginate(20);
        $statuses = MemberStatus::all();

        return view('admin.member.index', [
            'members' => $members,
            'statuses' => $statuses,
            'search' => $search,
            'filterStatus' => $filterStatus,
        ]);
    }

    public function create(): View
    {
        $villages = Village::orderBy('VILLAGE_NAME')->get();
        $districts = District::orderBy('DISTRICT_NAME')->get();
        $cities = City::orderBy('CITY_NAME')->get();
        $typeCities = TypeCity::all();
        $statuses = MemberStatus::all();

        return view('admin.member.create', compact('villages', 'districts', 'cities', 'typeCities', 'statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'MEMBER_NO' => 'required|max:50',
            'MEMBER_NAME' => 'required|max:200',
            'MEMBER_EMAIL' => 'nullable|email|max:200',
            'VILLAGE_ID' => 'nullable',
            'DISTRICT_ID' => 'nullable',
            'CITY_ID' => 'nullable',
            'MEMBER_STATUS_ID' => 'required',
            'BANKING_ACCOUNT_NO' => 'nullable|max:100',
        ]);

        // Generate unique MEMBER_REGISTRY
        $baseRegistry = 'MEM' . date('ymd') . substr((string)time(), -4);
        do {
            $registry = $baseRegistry . strtoupper(substr(md5(uniqid()), 0, 4));
        } while (Member::where('MEMBER_REGISTRY', $registry)->exists());

        Member::create([
            'MEMBER_REGISTRY' => $registry,
            'MEMBER_NO' => $request->input('MEMBER_NO'),
            'MEMBER_NAME' => $request->input('MEMBER_NAME'),
            'MEMBER_EMAIL' => $request->input('MEMBER_EMAIL'),
            'ADDRESS_STAY' => $request->input('ADDRESS_STAY'),
            'VILLAGE_ID' => $request->input('VILLAGE_ID'),
            'DISTRICT_ID' => $request->input('DISTRICT_ID'),
            'CITY_ID' => $request->input('CITY_ID'),
            'MEMBER_STATUS_ID' => $request->input('MEMBER_STATUS_ID'),
            'ACTIVE_STATUS_ID' => 1,
            'BANKING_ACCOUNT_NO' => $request->input('BANKING_ACCOUNT_NO'),
            'MEMBER_DATE_REGISTRY' => now()->format('Y-m-d H:i:s'),
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);

        session()->flash('alert-success', 'Member berhasil ditambahkan.');
        return redirect()->route('admin.member.index');
    }

    public function show(string $registry): View
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)
            ->with(['village', 'district', 'city', 'savings', 'loans'])
            ->firstOrFail();

        $villages = Village::orderBy('VILLAGE_NAME')->get();
        $districts = District::orderBy('DISTRICT_NAME')->get();
        $cities = City::orderBy('CITY_NAME')->get();
        $typeCities = TypeCity::all();
        $statuses = MemberStatus::all();

        return view('admin.member.show', compact('member', 'villages', 'districts', 'cities', 'typeCities', 'statuses'));
    }

    public function edit(string $registry): View
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();
        $villages = Village::orderBy('VILLAGE_NAME')->get();
        $districts = District::orderBy('DISTRICT_NAME')->get();
        $cities = City::orderBy('CITY_NAME')->get();
        $typeCities = TypeCity::all();
        $statuses = MemberStatus::all();

        return view('admin.member.edit', compact('member', 'villages', 'districts', 'cities', 'typeCities', 'statuses'));
    }

    public function update(Request $request, string $registry): RedirectResponse
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        $request->validate([
            'MEMBER_NO' => 'required|max:50',
            'MEMBER_NAME' => 'required|max:200',
            'MEMBER_EMAIL' => 'nullable|email|max:200',
            'MEMBER_STATUS_ID' => 'required',
        ]);

        $member->update([
            'MEMBER_NO' => $request->input('MEMBER_NO'),
            'MEMBER_NAME' => $request->input('MEMBER_NAME'),
            'MEMBER_EMAIL' => $request->input('MEMBER_EMAIL'),
            'ADDRESS_STAY' => $request->input('ADDRESS_STAY'),
            'VILLAGE_ID' => $request->input('VILLAGE_ID'),
            'DISTRICT_ID' => $request->input('DISTRICT_ID'),
            'CITY_ID' => $request->input('CITY_ID'),
            'MEMBER_STATUS_ID' => $request->input('MEMBER_STATUS_ID'),
            'BANKING_ACCOUNT_NO' => $request->input('BANKING_ACCOUNT_NO'),
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);

        session()->flash('alert-success', 'Data member berhasil diperbarui.');
        return redirect()->route('admin.member.index');
    }

    public function destroy(string $registry): RedirectResponse
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();
        $member->update(['ACTIVE_STATUS_ID' => 0, 'LOGTIME' => now()->format('Y-m-d H:i:s')]);

        session()->flash('alert-success', 'Member berhasil dinonaktifkan.');
        return redirect()->route('admin.member.index');
    }

    /**
     * Tampilkan halaman validasi anggota (berkas: KTP, KK, TTD, FOTO)
     *
     * @param string $type 'NTR' atau 'ALB' — menentukan view yang ditampilkan
     * @param string $registry Nomor registrasi anggota
     * @return View
     */
    public function validasi(string $type, string $registry): View
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        // Ambil berkas gambar (foto/scan) yang dimiliki anggota, per jenis.
        // Ini dipakai di view untuk memastikan berkas lengkap sebelum validasi.
        $berkas = $member->images()->get()->keyBy('IMAGE_TYPE');

        // View berbeda per tipe: NTR vs ALB (mengikuti naming CI3 lama)
        $viewName = $type === 'ALB' ? 'admin.member.validasi_alb' : 'admin.member.validasi_ntr';

        return view($viewName, [
            'member'   => $member,
            'berkas'   => $berkas,
            'type'     => $type,
            'registry' => $registry,
        ]);
    }

    /**
     * Proses validasi anggota — ubah status + catat tanggal verifikasi.
     *
     * Logika diambil dari CI3 Members::validasi_Anggota:
     *   ALB -> MEMBER_STATUS_ID = 10
     *   NTR -> MEMBER_STATUS_ID = 4
     * Keduanya: set LOGTIME, MEMBER_DATE_VERIFICATION, dan UPDATE_BY (user yang login).
     *
     * @param string $type     'NTR' atau 'ALB'
     * @param string $registry Nomor registrasi anggota
     * @return RedirectResponse
     */
    public function prosesValidasi(string $type, string $registry): RedirectResponse
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        // Status target berbeda per tipe (jaga-jaga: default NTR kalau tidak 'ALB')
        $statusTarget = ($type === 'ALB') ? 10 : 4;

        $member->update([
            'MEMBER_STATUS_ID'          => $statusTarget,
            'MEMBER_DATE_VERIFICATION'  => now()->format('Y-m-d'),
            // UPDATE_BY diisi dari user yang sedang login (pengganti session di CI3)
            'UPDATE_BY'                 => $this->userName,
            'LOGTIME'                   => now()->format('Y-m-d H:i:s'),
        ]);

        $this->alert('alert-success', "Anggota berhasil divalidasi sebagai anggota {$type}.");
        return redirect()->route('admin.member.index');
    }

    /**
     * Tampilkan laporan jumlah anggota berdasarkan status.
     * Logika mengikuti CI3 Anggota.php::lapJumlahAgt (filter ACTIVE_STATUS_ID=3)
     * dan cetakJmlhAgt.
     *
     * @param int $status 1=Aktif, 2=Bekukan, 3=PD
     * @return View
     */
    public function lapJumlahAnggota(int $status = 3): View
    {
        $data = Member::lapJumlahAnggota($status);

        return view('admin.member.lap_jumlah_anggota', [
            'data' => $data,
            'status' => $status,
            'jumlah' => count($data),
        ]);
    }

    /**
     * Export laporan jumlah anggota ke Excel.
     *
     * @param int $status 1=Aktif, 2=Bekukan, 3=PD
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelLapJumlahAnggota(int $status = 3)
    {
        $rows = Member::lapJumlahAnggota($status);

        // Header sesuai kebutuhan laporan jumlah anggota
        $headers = [
            'No. Reg', 'No. Anggota', 'Nama', 'Alamat',
            'Status Aktif', 'Tanggal Daftar',
        ];

        // Ubah data ke format array (tanpa object property)
        $labelStatus = match($status) {
            1 => 'Aktif',
            2 => 'Dibekukan',
            3 => 'PD',
            default => 'Status ' . $status,
        };

        $flat = array_map(fn($r) => [
            $r->MEMBER_REGISTRY,
            $r->MEMBER_NO,
            $r->MEMBER_NAME,
            $r->MEMBER_ADDRESS,
            $labelStatus,
            $r->MEMBER_DATE_REGISTRY,
        ], $rows);

        $label = match($status) {
            1 => 'aktif',
            2 => 'bekukan',
            3 => 'pd',
            default => 'status_' . $status,
        };

        // ExcelService::export() menerima array baris + array header (sudah import via FQN)
        return ExcelService::export($flat, $headers, "lap_jumlah_anggota_{$label}");
    }

    /**
     * Tampilkan form proses PD (Pengunduran Diri) atau Pembekuan anggota.
     *
     * Logika diambil dari CI3 Anggota.php:
     * - PD       -> form_pd_anggota (input: id_agt, alasanPD, jnsClose, areaAlasan)
     * - Bekukan  -> form pembekuan  (input: id_agt, areaAlasan)
     *
     * @param string $action 'pd' atau 'bekukan'
     * @param string $registry Nomor registrasi anggota
     * @return View
     */
    public function formPdBekukan(string $action, string $registry): View
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        // Opsi alasan PD sama seperti di CI3 form_pd_anggota
        $opsiAlasanPD = [
            '1' => 'Pindah',
            '2' => 'PHK/Ekonomi',
            '3' => 'Sakit',
            '4' => 'Usia Lanjut',
            '5' => 'Meninggal',
            '6' => 'Kondit',
            '7' => 'Lainnya',
        ];

        // Opsi jenis close (untuk PD)
        $opsiJnsClose = [
            '1' => 'Klop',
            '2' => 'Lebih',
            '3' => 'Tambah',
            '4' => 'CA',
        ];

        return view('admin.member.form_pd_bekukan', [
            'member'       => $member,
            'action'       => $action,
            'registry'     => $registry,
            'opsiAlasanPD' => $opsiAlasanPD,
            'opsiJnsClose' => $opsiJnsClose,
        ]);
    }

    /**
     * Proses PD (Pengunduran Diri) — ubah status member menjadi non-aktif.
     *
     * Logika diambil dari CI3 Members::prosesPD (di Anggota.php prosesPD):
     *   - ACTIVE_STATUS_ID = 3
     *   - SUMMARY_CLOSE_ID, TYPE_CLOSE_ID, DESCRIPT_SUMMARY, DATE_CLOSE (opsional)
     *
     * @param string $registry Nomor registrasi anggota
     * @return RedirectResponse
     */
    public function prosesPD(string $registry): RedirectResponse
    {
        $request = request();
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        // Base update: status non-aktif + logtime
        $updateData = [
            'ACTIVE_STATUS_ID' => 3,
            'UPDATE_BY'        => $this->userName,
            'LOGTIME'          => now()->format('Y-m-d H:i:s'),
        ];

        // Detect kolom tambahan yang tersedia di skema (opsi 1: aman)
        if (Schema::hasColumn('ma_member', 'SUMMARY_CLOSE_ID') && $request->input('alasanPD')) {
            $updateData['SUMMARY_CLOSE_ID'] = $request->input('alasanPD');
        }
        if (Schema::hasColumn('ma_member', 'TYPE_CLOSE_ID') && $request->input('jnsClose')) {
            $updateData['TYPE_CLOSE_ID'] = $request->input('jnsClose');
        }
        if (Schema::hasColumn('ma_member', 'DESCRIPT_SUMMARY') && $request->input('areaAlasan')) {
            $updateData['DESCRIPT_SUMMARY'] = $request->input('areaAlasan');
        }
        if (Schema::hasColumn('ma_member', 'DATE_CLOSE')) {
            $updateData['DATE_CLOSE'] = now()->format('Y-m-d');
        }

        $member->update($updateData);

        $this->alert('alert-success', 'Proses PD (Pengunduran Diri) berhasil disimpan.');
        return redirect()->route('admin.member.index');
    }

    /**
     * Proses Pembekuan — ubah status member menjadi dibekukan.
     *
     * Logika diambil dari CI3 Members::prosesPembekuanAgt:
     *   - ACTIVE_STATUS_ID = 2
     *   - SUMMARY_CLOSE_ID = 6 (hardcoded)
     *   - DESCRIPT_SUMMARY (input areaAlasan)
     *   - DATE_DEACTIVATE = hari ini
     *
     * @param string $registry Nomor registrasi anggota
     * @return RedirectResponse
     */
    public function prosesPembekuan(string $registry): RedirectResponse
    {
        $request = request();
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        // Base update: status dibekukan + logtime
        $updateData = [
            'ACTIVE_STATUS_ID' => 2,
            'SUMMARY_CLOSE_ID' => 6, // hardcoded sesuai CI3
            'UPDATE_BY'        => $this->userName,
            'LOGTIME'          => now()->format('Y-m-d H:i:s'),
        ];

        // Detect kolom tambahan yang tersedia di skema (opsi 1: aman)
        if (Schema::hasColumn('ma_member', 'DESCRIPT_SUMMARY') && $request->input('areaAlasan')) {
            $updateData['DESCRIPT_SUMMARY'] = $request->input('areaAlasan');
        }
        if (Schema::hasColumn('ma_member', 'DATE_DEACTIVATE')) {
            $updateData['DATE_DEACTIVATE'] = now()->format('Y-m-d');
        }

        $member->update($updateData);

        $this->alert('alert-success', 'Proses pembekuan berhasil disimpan.');
        return redirect()->route('admin.member.index');
    }

    /**
     * Cetak / Unduh KTA anggota dalam format PDF.
     *
     * Logika mengikuti CI3 Anggota.php::cetak_KTA + ktaCetakPDF:
     * ambil data member + foto (PHOTO), lalu generate PDF via PdfService (DomPDF).
     *
     * @param string $registry Nomor registrasi anggota
     * @return \Illuminate\Http\Response
     */
    public function cetakKTA(string $registry): \Illuminate\Http\Response
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        // Ambil foto (PHOTO) anggota — dipakai di KTA.
        // Foto di ma_images.IMAGE_FILE disimpan sebagai base64 string (sesuai CI3).
        $foto = $member->images()
            ->where('IMAGE_TYPE', 'PHOTO')
            ->where('IMAGE_FILE', '!=', '')
            ->first();

        // Susun data untuk view PDF
        $kta = [
            'noAnggota'  => $member->MEMBER_NO,
            'nama'       => $member->MEMBER_NAME,
            // MEMBER_BIRTHPLACE & MEMBER_BIRTHDAY tidak ada di skema ma_member (lihat simkopukm_schema.sql)
            // → pakai nilai null, view akan handle secara graceful
            'ttl'        => null,
            'alamat'     => $member->ADDRESS_STAY,
            'tglMasuk'   => \Carbon\Carbon::parse($member->MEMBER_DATE_REGISTRY)->format('d-m-Y'),
            'noRegistry' => $member->MEMBER_REGISTRY,
            'foto'       => $foto ? $foto->IMAGE_FILE : null,
            'tglCetak'   => now()->format('d-m-Y'),
        ];

        return PdfService::generate('pdf.member.kta', ['kta' => $kta], 'KTA_' . $registry);
    }

    /**
     * Tampilkan preview KTA (HTML) — sebelum diunduh sebagai PDF.
     *
     * @param string $registry Nomor registrasi anggota
     * @return View
     */
    public function previewKTA(string $registry): View
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        $foto = $member->images()
            ->where('IMAGE_TYPE', 'PHOTO')
            ->where('IMAGE_FILE', '!=', '')
            ->first();

        $kta = [
            'noAnggota'  => $member->MEMBER_NO,
            'nama'       => $member->MEMBER_NAME,
            'ttl'        => null,
            'alamat'     => $member->ADDRESS_STAY,
            'tglMasuk'   => \Carbon\Carbon::parse($member->MEMBER_DATE_REGISTRY)->format('d-m-Y'),
            'noRegistry' => $member->MEMBER_REGISTRY,
            'foto'       => $foto ? $foto->IMAGE_FILE : null,
            'tglCetak'   => now()->format('d-m-Y'),
        ];

        return view('admin.member.preview_kta', ['kta' => $kta, 'registry' => $registry]);
    }

    /**
     * Tampilkan form upload berkas anggota (KTP, KK, FOTO, TTD).
     *
     * Logika mengikuti CI3 Anggota::loadBerkas + view_upload.php:
     * tampilkan form 4 input file; saat submit, base64-encode & simpan ke ma_images.
     *
     * @param string $registry Nomor registrasi anggota
     * @return View
     */
    public function berkas(string $registry): View
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        // Ambil status berkas yang sudah ada (untuk pre-fill checkbox / info)
        $berkasStatus = [
            'KTP'   => Image::checkImage($registry, 'KTP'),
            'KK'    => Image::checkImage($registry, 'KK'),
            'PHOTO' => Image::checkImage($registry, 'PHOTO'),
            'TTD'   => Image::checkImage($registry, 'TTD'),
        ];

        // Ambil gambar yang sudah ter-upload (untuk preview)
        $berkas = $member->images()->get()->keyBy('IMAGE_TYPE');

        return view('admin.member.berkas', compact('member', 'registry', 'berkasStatus', 'berkas'));
    }

    /**
     * Proses upload berkas anggota — simpan base64 gambar ke ma_images.
     *
     * Logika mengikuti CI3 Anggota::simpan_Anggota (bagian image_base64 + upload_foto):
     * - images_1 = KTP
     * - images_2 = PHOTO
     * - images_3 = TTD
     * - images_4 = KK
     *
     * @param string $registry Nomor registrasi anggota
     * @return RedirectResponse
     */
    public function prosesBerkas(string $registry): RedirectResponse
    {
        $member = Member::where('MEMBER_REGISTRY', $registry)->firstOrFail();

        $fieldMap = [
            'images_1' => 'KTP',
            'images_2' => 'PHOTO',
            'images_3' => 'TTD',
            'images_4' => 'KK',
        ];

        foreach ($fieldMap as $field => $type) {
            $file = request()->file($field);

            if ($file && $file->isValid()) {
                // Validasi: hanya gambar, maks 2 MB
                if (!$file->getSize() || $file->getSize() > 2 * 1024 * 1024) {
                    $this->alert('alert', "File {$type} melebihi 2 MB atau tidak valid.");
                    return redirect()->route('admin.member.berkas', $registry);
                }
                if (!in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $this->alert('alert', "File {$type} harus berformat JPG, PNG, atau GIF.");
                    return redirect()->route('admin.member.berkas', $registry);
                }

                $base64 = base64_encode(file_get_contents($file->getRealPath()));
                Image::uploadFoto($registry, $type, $base64);
            }
        }

        $this->alert('alert-success', 'Berkas anggota berhasil disimpan.');
        return redirect()->route('admin.member.show', $registry);
    }
}
