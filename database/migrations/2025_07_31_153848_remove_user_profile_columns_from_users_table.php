<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: supprimer les colonnes.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'location',
                'job_title',
                'portfolio_url',
                'availability',
                'profile_photo',
                'about',
            ]);
        });
    }

    /**
     * Reverse the migrations: recréer les colonnes si besoin.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('location')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('location');
            $table->string('portfolio_url')->nullable()->after('job_title');
            $table->string('availability')->nullable()->after('portfolio_url');
            $table->string('profile_photo')->nullable()->after('availability');
            $table->text('about')->nullable()->after('profile_photo');
        });
    }
};
