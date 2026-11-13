<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Peranan Pentadbir Zon dibuang: keputusan akhir selepas lawatan tapak kini
 * dibuat oleh Pentadbir JAWI.
 *
 * - Peranan 'zon' dikeluarkan daripada semua pengguna dan dipadam.
 * - Pengguna yang tidak lagi mempunyai sebarang peranan dinyahaktifkan
 *   (bukan dipadam) supaya rekod & jejak audit kekal.
 *
 * Rekod pengesahan sedia ada dengan peringkat 'ZON' dalam
 * application_confirmations dikekalkan kerana ia kini mewakili
 * keputusan akhir permohonan.
 */
return new class extends Migration
{
    public function up(): void
    {
        $roleId = DB::table('roles')->where('slug', 'zon')->value('id');

        if (!$roleId) {
            return;
        }

        $affectedUserIds = DB::table('role_user')
            ->where('role_id', $roleId)
            ->pluck('user_id');

        DB::table('role_user')->where('role_id', $roleId)->delete();
        DB::table('roles')->where('id', $roleId)->delete();

        $usersWithoutRole = DB::table('users')
            ->whereIn('id', $affectedUserIds)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('role_user')
                    ->whereColumn('role_user.user_id', 'users.id');
            })
            ->pluck('id');

        DB::table('users')
            ->whereIn('id', $usersWithoutRole)
            ->update(['is_active' => false]);
    }

    public function down(): void
    {
        // Peranan dicipta semula; penetapan peranan kepada pengguna perlu dibuat secara manual.
        if (!DB::table('roles')->where('slug', 'zon')->exists()) {
            DB::table('roles')->insert([
                'slug'       => 'zon',
                'name'       => 'Pentadbir Pusat Zon',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
