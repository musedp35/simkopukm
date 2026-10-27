<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'ma_images';
    public $timestamps = false;

    protected $fillable = [
        'USER_ID',
        'IMAGE_FILE',
        'IMAGE_TYPE',
        'LOGTIME',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'USER_ID', 'MEMBER_REGISTRY');
    }

    /**
     * Upload (insert-or-update) gambar per anggota & jenis.
     * Logika sama dengan CI3 Images::upload_foto:
     *   - Jika belum ada → insert
     *   Jika sudah ada & image_file tidak kosong → update
     *
     * @param string $userId    USER_ID (= MEMBER_REGISTRY)
     * @param string $type      IMAGE_TYPE (KTP / KK / PHOTO / TTD)
     * @param string $imageFile base64 encoded string
     * @return bool
     */
    public static function uploadFoto(string $userId, string $type, string $imageFile): bool
    {
        $existing = static::where('USER_ID', $userId)
            ->where('IMAGE_TYPE', $type)
            ->first();

        if (!$existing) {
            return (bool) static::create([
                'USER_ID'    => $userId,
                'IMAGE_TYPE' => $type,
                'IMAGE_FILE' => $imageFile,
                'LOGTIME'    => now()->format('Y-m-d H:i:s'),
            ]);
        }

        if ($imageFile !== '') {
            $existing->update([
                'IMAGE_FILE' => $imageFile,
                'LOGTIME'    => now()->format('Y-m-d H:i:s'),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Cek apakah gambar sudah ada untuk user & jenis tertentu.
     * Logika sama dengan CI3 Images::checkImages.
     *
     * @param string $userId
     * @param string $type
     * @return array ['notif' => string, 'code' => string]
     */
    public static function checkImage(string $userId, string $type): array
    {
        $total = static::where('USER_ID', $userId)
            ->where('IMAGE_TYPE', $type)
            ->where('IMAGE_FILE', '!=', '')
            ->count();

        return [
            'notif' => $total > 0 ? 'Data sudah ada' : 'Data belum ada',
            'code'  => $total > 0 ? '1' : '0',
        ];
    }
}
