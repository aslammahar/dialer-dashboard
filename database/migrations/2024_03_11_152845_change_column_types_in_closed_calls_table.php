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
        Schema::table('closed_calls', function (Blueprint $table) {
            
            $table->text('customer_full_name')->nullable()->change();
            $table->text('phone_number')->nullable()->change();
            $table->text('alternate_phone_number')->nullable()->change();
            $table->text('cx_email')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->text('city')->nullable()->change();
            $table->text('state')->nullable()->change();
            $table->text('zip_code')->nullable()->change();
            $table->text('gender')->nullable()->change();
            $table->text('martial_status')->nullable()->change();
            $table->integer('age')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->text('palce_of_birth')->nullable()->change();
            $table->text('height_weight')->nullable()->change();
            $table->text('social_security')->nullable()->change();
            $table->text('smoker')->nullable()->change();
            $table->text('health_condition')->nullable()->change();
            $table->text('medication')->nullable()->change();
            $table->text('hospital_name')->nullable()->change();
            $table->text('hospital_address')->nullable()->change();
            $table->text('physician_name')->nullable()->change();
            $table->text('monthly_premium')->nullable()->change();
            $table->text('carrier')->nullable()->change();
            $table->text('coverage_plan')->nullable()->change();
            $table->text('customer_eligibility')->nullable()->change();
            $table->text('beneficiary')->nullable()->change();
            $table->text('beneficiary_relation')->nullable()->change();
            $table->text('beneficiary_phone')->nullable()->change();
            $table->date('beneficiary_dob')->nullable()->change();
            $table->text('payor')->nullable()->change();
            $table->text('bank_name')->nullable()->change();
            $table->text('bank_address')->nullable()->change();
            $table->text('routing_number')->nullable()->change();
            $table->text('bank_account_number')->nullable()->change();
            $table->text('debit_card_direct_express_no')->nullable()->change();
            $table->text('debit_card_direct_express_expiration')->nullable()->change();
            $table->text('debit_card_direct_express_cvv')->nullable()->change();
            $table->text('account_type')->nullable()->change();
            $table->date('initial_draft_date')->nullable()->change();
            $table->date('future_draft_date')->nullable()->change();
            $table->text('underwriter_name')->nullable()->change();
            $table->text('remarks')->nullable()->change();
            $table->unsignedBigInteger('closer_id')->change();
            $table->text('junior_closer_name')->nullable()->change();
            $table->text('center_name')->nullable()->change();
            $table->text('sale_made_by')->nullable()->change();
            
            $table->text('clients_comment')->nullable()->change();
            $table->unsignedBigInteger('clients_id')->nullable()->change();

            


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            //
        });
    }
};
