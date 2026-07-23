<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('closed_calls', function (Blueprint $table) {
            $table->id();
            $table->string('customer_full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('alternate_phone_number')->nullable();
            $table->string('cx_email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('gender')->nullable();
            $table->string('martial_status')->nullable();
            $table->integer('age')->nullable();
            $table->date('dob')->nullable();
            $table->string('palce_of_birth')->nullable();
            $table->string('height_weight')->nullable();
            $table->text('social_security')->nullable();
            $table->string('smoker')->nullable();
            $table->string('health_condition')->nullable();
            $table->string('medication')->nullable();
            $table->string('hospital_name')->nullable();
            $table->string('hospital_address')->nullable();
            $table->string('physician_name')->nullable();
            $table->string('monthly_premium')->nullable();
            $table->string('carrier')->nullable();
            $table->string('coverage_plan')->nullable();
            $table->string('customer_eligibility')->nullable();
            $table->string('beneficiary')->nullable();
            $table->string('beneficiary_relation')->nullable();
            $table->string('beneficiary_phone')->nullable();
            $table->date('beneficiary_dob')->nullable();
            $table->string('payor')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_address')->nullable();
            $table->text('routing_number')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->text('debit_card_direct_express_no')->nullable();
            $table->string('debit_card_direct_express_expiration')->nullable();
            $table->string('debit_card_direct_express_cvv')->nullable();
            $table->string('account_type')->nullable();
            $table->date('initial_draft_date')->nullable();
            $table->date('future_draft_date')->nullable();
            $table->string('underwriter_name')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('closer_id');
            $table->string('junior_closer_name')->nullable();
            $table->string('center_name')->nullable();
            $table->string('sale_made_by')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'funded', 'charged_backed'])->default('pending');
            
            $table->string('clients_comment')->nullable();
            $table->unsignedBigInteger('clients_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closed_calls');
    }
};
