<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono')->nullable()->after('email');
            $table->string('ciudad_origen')->nullable()->after('telefono');
            $table->boolean('consentimiento_marketing')->default(false)->after('ciudad_origen');
            $table->string('avatar')->nullable()->after('consentimiento_marketing');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telefono', 'ciudad_origen', 'consentimiento_marketing', 'avatar']);
        });
    }
};
