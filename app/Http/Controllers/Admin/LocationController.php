<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Village;
use App\Models\District;
use App\Models\City;
use App\Models\TypeCity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends BaseAdminController
{
    // ===== VILLAGE =====
    public function villageIndex(): View
    {
        $villages = Village::with('district')->orderBy('VILLAGE_NAME')->paginate(20);
        $districts = District::orderBy('DISTRICT_NAME')->get();
        return view('admin.location.village_index', compact('villages', 'districts'));
    }

    public function villageCreate(): View
    {
        $districts = District::orderBy('DISTRICT_NAME')->get();
        return view('admin.location.village_create', compact('districts'));
    }

    public function villageStore(Request $request): RedirectResponse
    {
        $request->validate([
            'VILLAGE_NAME' => 'required|max:100',
            'DISTRICT_ID'  => 'nullable',
        ]);
        Village::create([
            'VILLAGE_ID'   => strtoupper(substr(md5(uniqid()), 0, 4)),
            'VILLAGE_NAME' => $request->input('VILLAGE_NAME'),
            'DISTRICT_ID'  => $request->input('DISTRICT_ID'),
        ]);
        session()->flash('alert-success', 'Desa/Kelurahan berhasil ditambahkan.');
        return redirect()->route('admin.location.village');
    }

    public function villageEdit(string $id): View
    {
        $village = Village::findOrFail($id);
        $districts = District::orderBy('DISTRICT_NAME')->get();
        return view('admin.location.village_edit', compact('village', 'districts'));
    }

    public function villageUpdate(Request $request, string $id): RedirectResponse
    {
        $village = Village::findOrFail($id);
        $request->validate(['VILLAGE_NAME' => 'required|max:100']);
        $village->update(['VILLAGE_NAME' => $request->input('VILLAGE_NAME'), 'DISTRICT_ID' => $request->input('DISTRICT_ID')]);
        session()->flash('alert-success', 'Desa/Kelurahan berhasil diperbarui.');
        return redirect()->route('admin.location.village');
    }

    public function villageDestroy(string $id): RedirectResponse
    {
        Village::where('VILLAGE_ID', $id)->delete();
        session()->flash('alert-success', 'Desa/Kelurahan berhasil dihapus.');
        return redirect()->route('admin.location.village');
    }

    // ===== DISTRICT =====
    public function districtIndex(): View
    {
        $districts = District::with('city')->orderBy('DISTRICT_NAME')->paginate(20);
        $cities = City::orderBy('CITY_NAME')->get();
        return view('admin.location.district_index', compact('districts', 'cities'));
    }

    public function districtCreate(): View
    {
        $cities = City::orderBy('CITY_NAME')->get();
        return view('admin.location.district_create', compact('cities'));
    }

    public function districtStore(Request $request): RedirectResponse
    {
        $request->validate([
            'DISTRICT_NAME' => 'required|max:100',
            'CITY_ID'       => 'nullable',
        ]);
        District::create([
            'DISTRICT_ID'   => strtoupper(substr(md5(uniqid()), 0, 4)),
            'DISTRICT_NAME' => $request->input('DISTRICT_NAME'),
            'CITY_ID'       => $request->input('CITY_ID'),
        ]);
        session()->flash('alert-success', 'Kecamatan berhasil ditambahkan.');
        return redirect()->route('admin.location.district');
    }

    public function districtEdit(string $id): View
    {
        $district = District::findOrFail($id);
        $cities = City::orderBy('CITY_NAME')->get();
        return view('admin.location.district_edit', compact('district', 'cities'));
    }

    public function districtUpdate(Request $request, string $id): RedirectResponse
    {
        $district = District::findOrFail($id);
        $request->validate(['DISTRICT_NAME' => 'required|max:100']);
        $district->update(['DISTRICT_NAME' => $request->input('DISTRICT_NAME'), 'CITY_ID' => $request->input('CITY_ID')]);
        session()->flash('alert-success', 'Kecamatan berhasil diperbarui.');
        return redirect()->route('admin.location.district');
    }

    public function districtDestroy(string $id): RedirectResponse
    {
        District::where('DISTRICT_ID', $id)->delete();
        session()->flash('alert-success', 'Kecamatan berhasil dihapus.');
        return redirect()->route('admin.location.district');
    }

    // ===== CITY =====
    public function cityIndex(): View
    {
        $cities = City::with('typeCity')->orderBy('CITY_NAME')->paginate(20);
        $typeCities = TypeCity::orderBy('TYPE_CITY_NAME')->get();
        return view('admin.location.city_index', compact('cities', 'typeCities'));
    }

    public function cityCreate(): View
    {
        $typeCities = TypeCity::orderBy('TYPE_CITY_NAME')->get();
        return view('admin.location.city_create', compact('typeCities'));
    }

    public function cityStore(Request $request): RedirectResponse
    {
        $request->validate([
            'CITY_NAME' => 'required|max:100',
            'TYPE_CITY_ID' => 'nullable',
        ]);
        City::create([
            'CITY_ID' => strtoupper(substr(md5(uniqid()), 0, 4)),
            'CITY_NAME' => $request->input('CITY_NAME'),
            'TYPE_CITY_ID' => $request->input('TYPE_CITY_ID'),
        ]);
        session()->flash('alert-success', 'Kota/Kabupaten berhasil ditambahkan.');
        return redirect()->route('admin.location.city');
    }

    public function cityEdit(string $id): View
    {
        $city = City::findOrFail($id);
        $typeCities = TypeCity::orderBy('TYPE_CITY_NAME')->get();
        return view('admin.location.city_edit', compact('city', 'typeCities'));
    }

    public function cityUpdate(Request $request, string $id): RedirectResponse
    {
        $city = City::findOrFail($id);
        $request->validate(['CITY_NAME' => 'required|max:100']);
        $city->update(['CITY_NAME' => $request->input('CITY_NAME'), 'TYPE_CITY_ID' => $request->input('TYPE_CITY_ID')]);
        session()->flash('alert-success', 'Kota/Kabupaten berhasil diperbarui.');
        return redirect()->route('admin.location.city');
    }

    public function cityDestroy(string $id): RedirectResponse
    {
        City::where('CITY_ID', $id)->delete();
        session()->flash('alert-success', 'Kota/Kabupaten berhasil dihapus.');
        return redirect()->route('admin.location.city');
    }
}
