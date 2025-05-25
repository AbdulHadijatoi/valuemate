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
        Schema::create('service_pricings', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('service_type_id')
                ->nullable()
                ->constrained('service_types')
                ->onDelete('cascade');
            
            $table->foreignId('property_type_id')
                ->nullable()
                ->constrained('property_types')
                ->onDelete('cascade');
            
            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->onDelete('cascade');
            
            $table->foreignId('request_type_id')
                ->default(1)
                ->constrained('request_types')
                ->onDelete('cascade');
            
            $table->double('area_from', 10, 3);
            $table->double('area_to', 10, 3);
            $table->double('price', 8, 3);
            $table->string('currency')->default('OMR');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_pricings');
    }
};
