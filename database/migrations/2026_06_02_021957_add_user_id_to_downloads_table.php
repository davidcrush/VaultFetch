<?php

use App\Models\Download;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const string ADMIN_EMAIL = 'david@davidcrush.com';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('downloads', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        $adminId = User::query()->where('email', self::ADMIN_EMAIL)->value('id');

        if ($adminId !== null) {
            Download::query()
                ->whereNull('user_id')
                ->update(['user_id' => $adminId]);
        }

        Schema::table('downloads', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('downloads', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
