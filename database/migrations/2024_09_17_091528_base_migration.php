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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->timestamps();
        });
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('src');
            $table->string('alt')->nullable();
            $table->timestamps();
        });
        Schema::create('product_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('description', 400)->nullable();
            $table->foreignId('image_id')->nullable()->constrained();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->default(0);
            $table->string('status', 60)->default('published');
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->foreignId('image_id')->nullable()->constrained();
            $table->timestamps();
        });
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('image_id')->nullable()->constrained();
            $table->timestamps();
        });
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('status')->default('published');
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->string('reference')->nullable();
            $table->integer('quantity')->unsigned()->nullable();
            $table->foreignId('image_id')->nullable()->constrained();
            $table->tinyInteger('allow_checkout_when_out_of_stock')->unsigned()->default(0);
            $table->tinyInteger('with_storehouse_management')->unsigned()->default(0);
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->foreignId('brand_id')->nullable();
            $table->tinyInteger('is_variation')->default(0);
            $table->tinyInteger('sale_type')->default(0);
            $table->double('price')->unsigned()->nullable();
            $table->double('sale_price')->unsigned()->nullable();
            $table->double('discount_price')->unsigned()->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->float('length')->nullable();
            $table->float('wide')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->foreignId('tax_id')->nullable();
            $table->foreignId('created_by_id')->nullable()->default(0);
            $table->string('created_by_type')->default(\App\Models\User::ADMIN_TYPE);
            $table->string('status', 60)->default('published');
            $table->integer('views')->default(0);
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->timestamps();
        });
        Schema::create('product_category', function (Blueprint $table) {
            $table->foreignId('product_id')->index();
            $table->foreignId('category_id')->index();
            $table->primary(['product_id', 'category_id'], 'product_category_primary_key');
            $table->timestamps();
        });
        Schema::create('label_products', function (Blueprint $table) {
            $table->foreignId('label_id')->index();
            $table->foreignId('product_id')->index();
            $table->primary(['label_id', 'product_id']);
        });
        Schema::create('product_related_relations', function (Blueprint $table) {
            $table->foreignId('from_product_id')->index();
            $table->foreignId('to_product_id')->index();
            $table->primary(['from_product_id', 'to_product_id'], 'product_related_primary_key');
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 400)->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });
        Schema::create('product_tag', function (Blueprint $table) {
            $table->foreignId('product_id')->index();
            $table->foreignId('tag_id')->index();

            $table->primary(['product_id', 'tag_id']);
        });
        Schema::create('product_image', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained();
            $table->foreignId('image_id')->constrained();
            $table->primary(['product_id', 'image_id']);
        });
        Schema::create('product_collection_products', function (Blueprint $table) {
            $table->foreignId('product_collection_id')->index();
            $table->foreignId('product_id')->index();
            $table->primary(['product_id', 'product_collection_id'], 'product_collections_product_primary_key');
        });
        Schema::create('product_attribute_sets', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('slug', 120)->nullable();
            $table->string('display_layout')->default('swatch_dropdown');
            $table->tinyInteger('is_searchable')->unsigned()->default(1);
            $table->tinyInteger('is_comparable')->unsigned()->default(1);
            $table->tinyInteger('is_use_in_product_listing')->unsigned()->default(0);
            $table->string('status', 60)->default('published');
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->timestamps();
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_set_id');
            $table->string('title', 120);
            $table->string('slug', 120)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('is_default')->unsigned()->default(0);
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('product_with_attribute_set', function (Blueprint $table) {
            $table->foreignId('attribute_set_id');
            $table->foreignId('product_id');
            $table->tinyInteger('order')->unsigned()->default(0);

            $table->primary(['product_id', 'attribute_set_id'], 'product_with_attribute_set_primary_key');
        });

        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable();
            $table->foreignId('configurable_product_id');
            $table->tinyInteger('is_default')->default(0);
        });

        Schema::create('product_variation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id');
            $table->foreignId('variation_id');

            $table->unique(['attribute_id', 'variation_id']);
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->float('percentage', 8, 6)->nullable();
            $table->integer('priority')->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('product_id');
            $table->float('star');
            $table->string('comment');
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('city_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // pickup, home_delivery, expedition
            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('type', ['pickup', 'distance']);

            $table->boolean('is_free')->default(false);

            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('price_per_km', 10, 2)->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('status', 120)->default('pending');
            $table->string('payment_status', 60)->default('pending');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('tax_amount')->nullable();
            $table->decimal('shipping_amount')->nullable();
            $table->text('description')->nullable();
            $table->string('coupon_code', 120)->nullable();
            $table->decimal('discount_amount', 15)->nullable();
            $table->decimal('sub_total', 15);
            $table->boolean('is_confirmed')->default(false);
            $table->string('discount_description')->nullable();
            $table->boolean('is_finished')->default(0)->nullable();
            $table->string('token', 120)->nullable();
            $table->string('payment_method', 60)->nullable();
            $table->foreignId('shipping_method_id')->nullable();

            $table->timestamps();
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->integer('qty');
            $table->decimal('price', 15);
            $table->decimal('tax_amount', 15);
            $table->text('options')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->string('product_name');
            $table->float('weight')->default(0)->nullable();
            $table->integer('restock_quantity', false, true)->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('address')->nullable();
            $table->foreignId('order_id');
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120)->nullable();
            $table->string('code', 20)->unique()->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('total_used')->unsigned()->default(0);
            $table->double('value')->nullable();
            $table->string('type', 60)->default('coupon')->nullable();
            $table->boolean('can_use_with_promotion')->default(false);
            $table->string('discount_on', 20)->nullable();
            $table->integer('product_quantity', false, true)->nullable();
            $table->string('type_option', 100)->default('amount');
            $table->string('target', 100)->default('all-orders');
            $table->decimal('min_order_price', 15)->nullable();
            $table->timestamps();
        });

        Schema::create('wish_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('product_id');
            $table->timestamps();
        });

        // -------------------
        // MEDIA (compatible Spatie/Medialibrary)
        // -------------------
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // model_type + model_id
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations')->nullable();
            $table->json('custom_properties')->nullable();
            $table->json('generated_conversions')->nullable();
            $table->json('responsive_images')->nullable();
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->foreignId('image_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('href')->nullable();
            $table->string('btn_text')->nullable();
            $table->text('description')->nullable();

            // Ordre manuel
            $table->unsignedInteger('position')->default(0);

            $table->boolean('is_active')->default(true);
            $table->index(['is_active', 'position']);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->foreignId('image_id')->constrained()->cascadeOnDelete();

            $table->string('href')->nullable();
            $table->string('placement'); // home_top, home_middle, category_sidebar

            $table->boolean('is_active')->default(true);
            $table->index(['placement', 'is_active']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
