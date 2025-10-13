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
            $table->string('type')->nullable(); // attendee, vendor, sponsor

            // Common fields
            $table->string('full_name')->nullable();
            $table->string('mobile_number')->nullable();

            // Vendor-specific
            $table->string('company_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('products_to_sell')->nullable();
            $table->string('product_category')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('office_address')->nullable();

            // Sponsor-specific
            $table->string('sponsor_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'full_name',
                'mobile_number',
                'birthday',
                'company_name',
                'brand_name',
                'products_to_sell',
                'product_category',
                'contact_person_name',
                'office_address',
                'sponsor_name',
            ]);
        });
    }
};
