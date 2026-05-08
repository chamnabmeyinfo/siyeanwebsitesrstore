<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('slug')->unique();
            $table->string('model');
            $table->unsignedInteger('storage_capacity')->default(0);
            $table->string('color');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('list_price', 12, 2)->default(0);
            $table->decimal('online_price', 12, 2)->nullable();
            $table->integer('quantity_on_hand')->default(0);
            $table->string('hero_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->text('web_description')->nullable();
            $table->boolean('visible_online')->default(true);
            $table->timestamps();

            $table->index('model');
            $table->index('visible_online');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->index(['name', 'email', 'phone']);
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('payment_method', 32)->default('cash');
            $table->text('notes')->nullable();
            $table->timestamp('sold_at')->useCurrent();
            $table->timestamps();

            $table->index('sold_at');
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time', 32)->nullable();
            $table->string('status', 32)->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('converted_sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('store_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('href');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_menu_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('products');
    }
};
