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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
             $table->string('title');
           
            $table->text('description');
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->enum('visibility', ['public',  'private'])->default('public');
             $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};