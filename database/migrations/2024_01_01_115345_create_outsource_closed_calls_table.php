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
        Schema::create('outsource_closed_calls', function (Blueprint $table) {
            $table->id();
            $table->string('customer_full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('alternate_phone_number')->nullable();
            $table->string('cx_email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('gender')->nullable();
            $table->string('martial_status')->nullable();
            $table->integer('age')->nullable();
            $table->date('dob')->nullable();
            $table->string('palce_of_birth')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->text('social_security')->nullable();
            $table->string('smoker')->nullable();
            $table->text('health_condition')->nullable();
            $table->text('medication')->nullable();
            $table->string('hospital_name')->nullable();
            $table->text('hospital_address')->nullable();
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
            $table->text('bank_address')->nullable();
            $table->string('routing_number')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->text('debit_card_direct_express_no')->nullable();
            $table->string('debit_card_direct_express_expiration')->nullable();
            $table->string('debit_card_direct_express_cvv')->nullable();
            $table->string('account_type')->nullable();
            $table->date('initial_draft_date')->nullable();
            $table->date('future_draft_date')->nullable();
            $table->string('underwriter_name')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('closer_id')->nullable();
            $table->string('junior_closer_name')->nullable();
            $table->string('center_name')->nullable();
            $table->string('sale_made_by')->nullable();
            $table->string('status')->default('pending');
            $table->text('clients_comment')->nullable();
            $table->unsignedBigInteger('clients_id')->nullable();
            $table->string('closername')->nullable();
            $table->string('juniorcloser2')->nullable();
            $table->string('lead_id')->nullable();
            $table->string('teamname')->nullable();
            $table->string('agentname')->nullable();
            $table->string('dialeragentname')->nullable();
            $table->string('dialername')->nullable();
            $table->string('list_id_2')->nullable();
            $table->string('list_id_1')->nullable();
            $table->string('recording_id')->nullable();
            $table->string('hippa_id')->nullable();
            $table->string('policy_id')->nullable();
            $table->string('signature_type')->nullable();
            $table->string('call_id')->nullable();
            $table->string('dialer_name_new')->nullable();
            $table->string('client_name_2')->nullable();
            $table->string('agent_status')->nullable();
            $table->string('recording_status')->nullable();
            $table->timestamps();
            
            // Foreign key constraints (optional)
            $table->foreign('closer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('clients_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outsource_closed_calls');
    }
};