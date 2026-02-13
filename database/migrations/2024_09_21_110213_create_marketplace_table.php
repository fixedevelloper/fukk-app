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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email', 60)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('neighborhood', 120)->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('vendor_id')->nullable()->constrained('users','id');
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('description', 400)->nullable();
            $table->longText('content')->nullable();
            $table->string('status', 60)->default('published');
            $table->dateTime('vendor_verified_at')->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('certificate_file')->nullable();
            $table->string('government_id_file')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

        });
        Schema::create('mp_vendor_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users','id');
            $table->decimal('balance', 15)->default(0);
            $table->decimal('total_fee', 15)->default(0);
            $table->decimal('total_revenue', 15)->default(0);
            $table->string('signature')->nullable();
            $table->text('bank_info')->nullable();
            $table->timestamps();
        });


        Schema::create('store_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete(); // lien avec la commande globale
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2)->default(0); // total pour cette boutique
            $table->string('status', 120)->default('pending'); // statut de la boutique
            $table->string('payment_status', 60)->default('pending'); // paiement de la boutique
            $table->timestamps();
        });

        Schema::table('order_product', function (Blueprint $table) {
            $table->foreignId('store_order_id')->constrained('store_orders')->cascadeOnDelete();
        });


        Schema::table('discounts', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('vendor_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users','id');
            $table->foreignId('order_id')->nullable();
            $table->decimal('sub_amount', 15)->default(0)->unsigned()->nullable();
            $table->decimal('fee', 15)->default(0)->unsigned()->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('current_balance', 15)->default(0)->unsigned()->nullable();
            $table->string('currency', 120)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vendor_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users','id');
            $table->decimal('fee', 15)->default(0)->unsigned()->nullable();
            $table->decimal('amount', 15, 2)->default(0);

            $table->decimal('current_balance', 15)->default(0)->unsigned()->nullable();
            $table->string('currency', 120)->nullable();
            $table->text('description')->nullable();
            $table->text('bank_info')->nullable();
            $table->string('payment_channel', 60)->nullable();
            $table->foreignId('user_id')->default(0);
            $table->string('status', 60)->default('pending');
            $table->text('images')->nullable();
            $table->timestamps();
        });
        Schema::create('vendor_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id');
            $table->foreignId('customer_id')->nullable()->constrained('users','id');
            $table->string('name', 60);
            $table->string('email', 60);
            $table->longText('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace');
    }
};
