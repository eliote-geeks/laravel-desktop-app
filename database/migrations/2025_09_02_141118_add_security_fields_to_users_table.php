<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            $table->json('security_settings')->nullable()->after('is_active');
            $table->timestamp('password_changed_at')->nullable()->after('security_settings');
            $table->integer('failed_login_attempts')->default(0)->after('password_changed_at');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone', 
                'last_login_at',
                'is_active',
                'security_settings',
                'password_changed_at',
                'failed_login_attempts',
                'locked_until'
            ]);
        });
    }
};
