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
        $user = User::query()->where('email', self::EMAIL)->first();

        if ($user === null) {
            return;
        }

        $password = config('vaultfetch.admin_password');

        if (blank($password)) {
            throw new RuntimeException(
                'VAULTFETCH_ADMIN_PASSWORD must be set in .env before running migrations.',
            );
        }

        $user->update([
            'password' => $password,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Password changes cannot be reversed.
    }
};
