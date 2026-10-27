<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

#[\AllowDynamicProperties]
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'ma_user';
    protected $primaryKey = 'MEMBER_REGISTRY';
    public $timestamps = false;

    protected $fillable = [
        'ROLE_ID', 'USERNAME', 'USER_STATUS',
        'MEMBER_REGISTRY', 'UPDATE_BY', 'LOGTIME',
    ];

    protected $hidden = ['PASSWORD'];

    // Laravel auth config: login key
    public function getAuthIdentifierName() { return 'USERNAME'; }

    public function getAuthIdentifier() { return $this->USERNAME; }

    public function getAuthPassword() { return $this->PASSWORD; }

    public function getAuthRememberToken() { return $this->remember_token ?? ''; }

    public function setAuthRememberToken($token, $value) {
        // Jangan set ke $attributes agar Eloquent tidak mencoba save
        // ke kolom DB 'remember_token' yang tidak ada di ma_user.
        $this->remember_token = $value;
    }

    // ===== Relationships =====

    public function member() { return $this->belongsTo(Member::class, 'MEMBER_REGISTRY', 'MEMBER_REGISTRY'); }
    public function role() { return $this->belongsTo(Role::class, 'ROLE_ID', 'ROLE_ID'); }
    public function images() { return $this->hasMany(Image::class, 'USER_ID', 'MEMBER_REGISTRY'); }

    // ===== Methods =====

    public function getUser()
    {
        return static::select('ma_user.*', 'mo.*', 'mm.*')
            ->join('ma_role as mo', 'mo.ROLE_ID = ma_user.ROLE_ID', 'left outer')
            ->join('ma_member as mm', 'mm.MEMBER_REGISTRY = ma_user.MEMBER_REGISTRY', 'left outer')
            ->get();
    }

    public function getUserImageDetail($noReg)
    {
        return static::select(
            'mo.ROLE_NAME', 'mo.ROLE_ID', 'img.IMAGE_FILE',
            'm.MEMBER_NAME', 'u.MEMBER_REGISTRY',
            'u.ROLE_ID', 'u.USER_STATUS', 'u.USERNAME'
        )
            ->from('ma_user as u')
            ->join('ma_images as img', 'img.USER_ID = u.MEMBER_REGISTRY', 'left outer')
            ->join('ma_role as mo', 'mo.ROLE_ID = u.ROLE_ID', 'left outer')
            ->join('ma_member as m', 'm.MEMBER_REGISTRY = u.MEMBER_REGISTRY', 'left outer')
            ->where('u.MEMBER_REGISTRY', $noReg)
            ->where('img.IMAGE_TYPE', 'PHOTO')
            ->first();
    }

    public function countUserRole($user)
    {
        return static::where('USERNAME', $user->USERNAME)->get()->toArray();
    }

    public function addNewUser($data)
    {
        $password = Hash::make($data['edtSandi']);
        $username = auth()->user()->USERNAME ?? '';
        foreach ($data['cmbRole'] as $i => $roleId) {
            static::create([
                'ROLE_ID' => $data['cmbRole'][$i + 1],
                'USERNAME' => $data['edtUsername'],
                'USER_STATUS' => $data['check'],
                'MEMBER_REGISTRY' => $data['edtNoRegUpdate'],
                'PASSWORD' => $password,
                'LOGTIME' => now()->format('Y-m-d H:i:s'),
                'UPDATE_BY' => $username,
            ]);
        }
    }

    public function updateUser($data)
    {
        $password = Hash::make($data['edtSandi']);
        $username = auth()->user()->USERNAME ?? '';
        foreach ($data['cmbRole'] as $i => $roleId) {
            static::where('MEMBER_REGISTRY', $data['edtNoRegUpdate'])
                ->update([
                    'ROLE_ID' => $data['cmbRole'][$i + 1],
                    'USERNAME' => $data['edtUsername'],
                    'USER_STATUS' => $data['check'],
                    'PASSWORD' => $password,
                    'UPDATE_BY' => $username,
                    'LOGTIME' => now()->format('Y-m-d H:i:s'),
                ]);
        }
        return true;
    }

    public function dropUser($id)
    {
        return static::where('MEMBER_REGISTRY', $id)->delete();
    }

    public function getUserMemberDetail($noReg)
    {
        return Member::where('MEMBER_REGISTRY', $noReg)->first();
    }

    public function getUserMember()
    {
        return Member::where('member_management_id', 4)
            ->select('MEMBER_NAME', 'MEMBER_REGISTRY')
            ->get()->toArray();
    }
}
