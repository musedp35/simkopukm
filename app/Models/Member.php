<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Member extends Model {
    protected $table = "ma_member";
    protected $primaryKey = "MEMBER_REGISTRY";
    protected $keyType = "string"; // MEMBER_REGISTRY bertipe string (mis. MEM001), bukan auto-increment int
    public $timestamps = false;
    public $incrementing = false; // Non-auto-increment
    protected $fillable = ["MEMBER_NO","MEMBER_NAME","MEMBER_REGISTRY","MEMBER_EMAIL","ADDRESS_STAY","VILLAGE_ID","DISTRICT_ID","CITY_ID","MEMBER_STATUS_ID","ACTIVE_STATUS_ID","MEMBER_DATE_REGISTRY","MEMBER_DATE_VERIFICATION","BANKING_ACCOUNT_NO","UPDATE_BY","LOGTIME"];
    public function address() { return $this->hasOne(MemberAddress::class, "MEMBER_REGISTRY", "MEMBER_REGISTRY"); }
    public function user() { return $this->hasMany(User::class, "MEMBER_REGISTRY", "MEMBER_REGISTRY"); }
    public function images() { return $this->hasMany(Image::class, "USER_ID", "MEMBER_REGISTRY"); }
    public function savings() { return $this->hasMany(Saving::class, "MEMBER_REGISTRY", "MEMBER_REGISTRY"); }
    public function loans() { return $this->hasMany(Loan::class, "MEMBER_REGISTRY", "MEMBER_REGISTRY"); }
    public function village() { return $this->belongsTo(Village::class, "VILLAGE_ID", "VILLAGE_ID"); }
    public function district() { return $this->belongsTo(District::class, "DISTRICT_ID", "DISTRICT_ID"); }
    public function city() { return $this->belongsTo(City::class, "CITY_ID", "CITY_ID"); }
    public function isNotLogin() { return auth()->guest(); }
    public function updateLastLogin($id) { DB::table("ma_user")->where("id", $id)->update(["last_login" => now()]); }
    /**
     * Laporan jumlah anggota (opsi 1: sesuai CI3 — hanya anggota yang punya berkas).
     * Filter berdasarkan ACTIVE_STATUS_ID, mirip Anggota.php::tampil_data_anggota('PD') di CI3.
     *
     * @param int $activeStatus 1=Aktif, 2=Bekukan, 3=PD
     * @return array Baris data anggota
     */
    public static function lapJumlahAnggota(int $activeStatus = 3): array
    {
        return DB::table("ma_member as m")
            ->select(
                "m.MEMBER_REGISTRY", "m.MEMBER_NO", "m.MEMBER_NAME",
                "m.ADDRESS_STAY", "v.VILLAGE_NAME", "d.DISTRICT_NAME",
                "t.TYPE_CITY_CODE", "c.CITY_NAME",
                "m.MEMBER_STATUS_ID", "m.ACTIVE_STATUS_ID", "m.MEMBER_DATE_REGISTRY",
                DB::raw("CONCAT(m.ADDRESS_STAY, ', ', v.VILLAGE_NAME, ', ', d.DISTRICT_NAME, ', ', t.TYPE_CITY_CODE, ' ', c.CITY_NAME) AS MEMBER_ADDRESS")
            )
            ->leftJoin("ma_village as v", "v.VILLAGE_ID", "=", "m.VILLAGE_ID")
            ->leftJoin("ma_district as d", "d.DISTRICT_ID", "=", "m.DISTRICT_ID")
            ->leftJoin("ma_city as c", "c.CITY_ID", "=", "m.CITY_ID")
            ->leftJoin("ma_type_city as t", "t.TYPE_CITY_ID", "=", "c.TYPE_CITY_ID")
            // Opsi 1: hanya anggota yang punya minimal 1 berkas (image_file tidak kosong)
            ->whereExists(function ($q) {
                $q->select(DB::raw("1"))
                  ->from("ma_images")
                  ->whereColumn("ma_images.USER_ID", "m.MEMBER_REGISTRY")
                  ->where("ma_images.IMAGE_FILE", "!=", "");
            })
            ->where("m.ACTIVE_STATUS_ID", $activeStatus)
            ->orderBy("m.MEMBER_REGISTRY")
            ->get()
            ->toArray();
    }
    public static function getTotAgtDashboard() { return (array)DB::table("ma_member")->select(DB::raw("COUNT(MEMBER_REGISTRY) AS totAgt"))->where("ACTIVE_STATUS_ID", 1)->first(); }
    public static function getTotNewAgtDashboard() { return (array)DB::table("ma_member")->select(DB::raw("COUNT(MEMBER_REGISTRY) AS totNewAgt"))->where(function($q) { $q->where("member_status_id", 10)->orWhere("member_status_id", 4); })->whereRaw("DATE_FORMAT(MEMBER_DATE_VERIFICATION, ?)", [date("Y")])->where("ACTIVE_STATUS_ID", 1)->first(); }
    public static function getTotCalonAgtDashboard() { return (array)DB::table("ma_member")->select(DB::raw("COUNT(MEMBER_REGISTRY) AS totCalonAgt"))->where(function($q) { $q->where("member_status_id", 3)->orWhere("member_status_id", 9); })->whereRaw("DATE_FORMAT(MEMBER_DATE_REGISTRY, ?)", [date("Y")])->where("ACTIVE_STATUS_ID", 1)->first(); }
    public function getTotAgtPDDashboard() { return DB::table($this->table)->select(DB::raw("COUNT(MEMBER_REGISTRY) AS totAgtPD"))->where("ACTIVE_STATUS_ID", 3)->whereRaw("MONTH(MEMBER_DATE_VERIFICATION) = ?", [date("m")])->first()->toArray(); }
    public function get_data_keluarga($idAgt) { return static::where("MEMBER_REGISTRY", $idAgt)->first()->toArray(); }
    public function tampilDataAnggotaGlobal() { return DB::table("ma_member as m")->select("m.MEMBER_NO","m.MEMBER_NAME","m.MEMBER_REGISTRY","m.ADDRESS_STAY","v.VILLAGE_NAME","d.DISTRICT_NAME","t.TYPE_CITY_CODE","c.CITY_NAME","msa.SAVINGS_ACCOUNT_NO","mms.MEMBER_STATUS_NAME",DB::raw("CONCAT(m.ADDRESS_STAY, ', ', v.VILLAGE_NAME, ', ', d.DISTRICT_NAME, ', ', t.TYPE_CITY_CODE, ' ', c.CITY_NAME) AS MEMBER_ADDRESS"),"msa.ACTIVE_ACCOUNT_ID")->leftJoin("ma_village as v","v.VILLAGE_ID","=","m.VILLAGE_ID")->leftJoin("ma_district as d","d.DISTRICT_ID","=","m.DISTRICT_ID")->leftJoin("ma_city as c","c.CITY_ID","=","m.CITY_ID")->leftJoin("ma_member_status as mms","mms.MEMBER_STATUS_ID","=","m.MEMBER_STATUS_ID")->leftJoin("mu_savings_account as msa","msa.MEMBER_REGISTRY","=","m.MEMBER_REGISTRY")->leftJoin("ma_type_city as t","t.TYPE_CITY_ID","=","c.TYPE_CITY_ID")->where("m.MEMBER_NO","!=","")->whereNotNull("m.BANKING_ACCOUNT_NO")->where(function($q) { $q->whereNull("msa.ACTIVE_ACCOUNT_ID")->orWhere("msa.ACTIVE_ACCOUNT_ID", 1); })->get()->toArray(); }
}
