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
        Schema::create('donation_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('poster_path')->nullable();
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->text('short_description')->nullable();
            $table->longText('content');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('donation_program_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('donator_name');
            $table->string('donator_email');
            $table->decimal('amount', 15, 2);
            $table->text('message')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
        });

        Schema::create('program_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_program_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('fund_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_program_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->timestamp('disbursed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_disbursements');
        Schema::dropIfExists('program_updates');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donation_programs');
    }
};
