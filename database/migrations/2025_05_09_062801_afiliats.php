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
        Schema::create('affiliate_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Básico, Plata, Oro...
            $table->decimal('commission_percentage', 5, 2);
            $table->unsignedInteger('min_reservations')->nullable();
            $table->unsignedInteger('min_clicks')->nullable();
            $table->decimal('min_total_earnings', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('affiliate_level_id')->constrained('affiliate_levels')->onDelete('cascade');
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->nullable(); // para mantener historial
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

        Schema::create('rental_properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location');
            $table->decimal('price_per_night', 8, 2);
            $table->unsignedInteger('max_guests')->default(1);
            $table->string('image_url')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
        // Taula d'enllaços d'afiliats
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('rental_properties')->onDelete('cascade');
            $table->string('target_url');
            $table->string('generated_url')->unique();
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('rental_properties')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Llogater
            $table->foreignId('affiliate_link_id')->nullable()->constrained('affiliate_links')->onDelete('set null'); // si ve d'un afiliat
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'charged'])->default('pending');
            $table->timestamps();
        });

        // Taula de comissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->string('description')->nullable();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();

        });


        Schema::create('property_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('rental_properties')->onDelete('cascade');
            $table->foreignId('affiliate_link_id')->constrained('affiliate_links')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_link_id')->constrained()->onDelete('cascade');
            $table->ipAddress('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
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
        Schema::dropIfExists('affiliate_clicks');
        Schema::dropIfExists('property_links');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('affiliate_links');
        Schema::dropIfExists('rental_properties');
        Schema::dropIfExists('affiliate_contracts');
        Schema::dropIfExists('affiliate_levels');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }

};