<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const string EMAIL = 'david@davidcrush.com';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (User::query()->where('email', self::EMAIL)->exists()) {
            return;
        }

        $password = config('vaultfetch.admin_password');

        if (blank($password)) {
            throw new RuntimeException(
                'VAULTFETCH_ADMIN_PASSWORD must be set in .env before running migrations.',
            );
        }

        User::query()->create([
            'name' => 'david',
            'email' => self::EMAIL,
            'password' => $password,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::query()->where('email', self::EMAIL)->delete();
    }
};
