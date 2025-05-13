<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Ej: admin, user, proveedor
            $table->string('display_name')->nullable(); // Nombre legible (opcional)
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('company_name')->nullable();
            $table->string('website_url')->nullable();
            $table->enum('status', ['active', 'pending', 'rejected'])->default('pending');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });


        /*         // Taula d'usuaris
                Schema::create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->string('password');
                    $table->rememberToken();
                    $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                    $table->timestamps();
                });

                // Taula d'afiliats
                Schema::create('affiliates', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->string('company_name');
                    $table->string('website_url');
                    $table->enum('status', ['pendent', 'aprovat', 'rebutjat'])->default('pendent');
                    $table->timestamps();
                }); */

        // Taula d'enllaços d'afiliats
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->string('target_url');
            $table->string('generated_url')->unique();
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->timestamps();
        });

        // Taula de comissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('description')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();
        });

        /* // (Opcional) Taula de sol·licituds d'afiliació
        Schema::create('affiliate_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('website_url');
            $table->enum('status', ['pendent', 'aprovat', 'rebutjat'])->default('pendent');
            $table->timestamps();
        }); */
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_requests');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('affiliate_links');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
