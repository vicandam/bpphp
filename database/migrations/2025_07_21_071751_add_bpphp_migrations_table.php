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
        // Add columns to existing 'users' table
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile_no')->nullable()->after('email');
            $table->date('birthday')->nullable()->after('mobile_no');
            $table->string('city_or_province')->nullable()->after('birthday');
            $table->string('country')->nullable()->after('city_or_province'); // if outside the Philippines
            $table->string('most_favorite_film')->nullable()->after('country');
            $table->string('most_favorite_song')->nullable()->after('most_favorite_film');
            $table->text('greatest_dream')->nullable()->after('most_favorite_song');
            $table->string('referral_code')->unique()->nullable()->after('password'); // Member's own referral code
            $table->unsignedBigInteger('referred_by_member_id')->nullable()->after('referral_code'); // Foreign key to users.id
            $table->boolean('is_marketing_agent')->default(false)->after('referred_by_member_id');
            $table->boolean('is_marketing_catalyst')->default(false)->after('is_marketing_agent');
            $table->boolean('is_angel_investor')->default(false)->after('is_marketing_catalyst');
            $table->boolean('is_golden_hearts_awardee')->default(false)->after('is_angel_investor');
            $table->decimal('bpp_wallet_balance', 10, 2)->default(0.00)->after('is_golden_hearts_awardee');
            $table->decimal('bpp_points_balance', 10, 2)->default(0.00)->after('bpp_wallet_balance');
            $table->string('virtual_membership_card_qr')->nullable()->after('bpp_points_balance'); // QR code for digital ticket badge
            $table->unsignedBigInteger('membership_type_id')->nullable()->after('virtual_membership_card_qr'); // Foreign key to membership_types.id

            $table->foreign('referred_by_member_id')->references('id')->on('users')->onDelete('set null');
        });

        // Create 'membership_types' table
        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "United Moviegoers and Musiclovers Dream Club International", etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add foreign key to 'users' table after 'membership_types' is created
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('membership_type_id')->references('id')->on('membership_types')->onDelete('set null');
        });

        // Create 'referrals' table
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referred_member_id');
            $table->decimal('amount_earned', 8, 2); // Php100 for every paid new member
            $table->timestamps();

            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_member_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create 'events' table
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Alive and Happy @50", "What Dreams May Come Film Screening"
            $table->text('description')->nullable();
            $table->date('event_date')->nullable();
            $table->time('event_time')->nullable();
            $table->string('venue')->nullable();
            $table->boolean('is_movie_screening')->default(false);
            $table->boolean('is_concert')->default(false);
            $table->boolean('is_seminar_workshop')->default(false);
            $table->boolean('is_other_event')->default(false);
            $table->decimal('ticket_price', 10, 2);
            $table->timestamps();
        });

        // Create 'tickets' table
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->string('ticket_code')->unique(); // QR code
            $table->boolean('is_redeemed')->default(false);
            $table->decimal('joy_points_earned', 8, 2)->default(0.00); // 1 joy point for every P500 worth of ticket
            $table->timestamp('purchase_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        // Create 'business_partners' table
        Schema::create('business_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('agreement_details')->nullable();
            $table->unsignedBigInteger('referred_by_user_id')->nullable(); // if referred by a marketing agent/catalyst
            $table->timestamps();

            $table->foreign('referred_by_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Create 'partner_products_services' table
        Schema::create('partner_products_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_partner_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('points_for_redemption', 10, 2)->nullable(); // Points needed to redeem
            $table->boolean('is_voucher')->default(false);
            $table->timestamps();

            $table->foreign('business_partner_id')->references('id')->on('business_partners')->onDelete('cascade');
        });

        // Create 'sponsors' table
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('sponsorship_type')->nullable(); // e.g., "Event Sponsorship", "Film Sponsorship"
            $table->decimal('amount_pledged', 10, 2)->nullable();
            $table->unsignedBigInteger('referred_by_user_id')->nullable(); // if referred by a marketing agent/catalyst
            $table->timestamps();

            $table->foreign('referred_by_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Create 'film_projects' table
        Schema::create('film_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "The Purpose: Stitched Hearts", "The Purpose: Father's Day"
            $table->text('description')->nullable();
            $table->string('status'); // e.g., "Pre-production", "Production", "Post-production", "Released"
            $table->decimal('target_fund_amount', 10, 2)->nullable();
            $table->decimal('total_net_theatrical_ticket_sales', 10, 2)->default(0.00);
            $table->timestamps();
        });


        // Create 'investments' table
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('investment_amount', 10, 2);
            $table->integer('number_of_shares'); // P10,000 = 1 Share
            $table->unsignedBigInteger('film_project_id')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('referred_by_user_id')->nullable(); // if referred by a marketing agent/catalyst
            $table->timestamp('investment_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('film_project_id')->references('id')->on('film_projects')->onDelete('set null');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
            $table->foreign('referred_by_user_id')->references('id')->on('users')->onDelete('set null');
        });


        // Create 'payouts' table
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // e.g., "Referral Rewards", "Marketing Agent Commission", "Catalyst Commission", "Angel Investor Share"
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // e.g., "Pending", "Paid"
            $table->timestamp('transaction_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create 'donations' table
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // if a registered member
            $table->string('donor_name')->nullable(); // If not a registered user.
            $table->string('donation_type'); // "Cash" or "In Kind (Products/Services)"
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('description')->nullable(); // For in-kind donations.
            $table->timestamp('donation_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by_member_id']);
            $table->dropForeign(['membership_type_id']);
            $table->dropColumn([
                'mobile_no',
                'birthday',
                'city_or_province',
                'country',
                'most_favorite_film',
                'most_favorite_song',
                'greatest_dream',
                'referral_code',
                'referred_by_member_id',
                'is_marketing_agent',
                'is_marketing_catalyst',
                'is_angel_investor',
                'is_golden_hearts_awardee',
                'bpp_wallet_balance',
                'bpp_points_balance',
                'virtual_membership_card_qr',
                'membership_type_id',
            ]);
        });
        Schema::dropIfExists('donations');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('investments');
        Schema::dropIfExists('film_projects');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('partner_products_services');
        Schema::dropIfExists('business_partners');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('events');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('membership_types');
    }
};
