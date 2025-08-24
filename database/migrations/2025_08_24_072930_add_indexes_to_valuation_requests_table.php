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
        Schema::table('valuation_requests', function (Blueprint $table) {
            // Add indexes on foreign key fields for better performance
            $table->index('company_id');
            $table->index('user_id');
            $table->index('status_id');
            $table->index('property_type_id');
            $table->index('service_type_id');
            $table->index('request_type_id');
            $table->index('location_id');
            $table->index('service_pricing_id');
            
            // Add index on reference field for faster lookups
            $table->index('reference');
            
            // Add composite indexes for common query patterns
            $table->index(['user_id', 'status_id']);
            $table->index(['company_id', 'status_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('valuation_requests', function (Blueprint $table) {
            // Remove all added indexes
            $table->dropIndex(['company_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['property_type_id']);
            $table->dropIndex(['service_type_id']);
            $table->dropIndex(['request_type_id']);
            $table->dropIndex(['location_id']);
            $table->dropIndex(['service_pricing_id']);
            $table->dropIndex(['reference']);
            $table->dropIndex(['user_id', 'status_id']);
            $table->dropIndex(['company_id', 'status_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
