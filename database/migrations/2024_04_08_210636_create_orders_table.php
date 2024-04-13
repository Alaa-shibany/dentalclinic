<?php

use App\Models\Doctor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('center_name');
            $table->string('veneer')->nullable();
            $table->string('crown')->nullable();
            $table->string('inlay_onlay')->nullable();
            $table->string('ceramicBuildUp')->nullable();
            $table->string('ceramicFacing')->nullable();
            $table->string('fullAnatomic')->nullable();
            $table->string('fullMetal')->nullable();
            $table->string('PFM')->nullable();
            $table->string('DSD')->nullable();
            $table->string('mockUp')->nullable();
            $table->string('printedModel')->nullable();
            $table->string('PMMA')->nullable();
            $table->string('upper_color')->nullable();
            $table->string('middle_color')->nullable();
            $table->string('lower_color')->nullable();
            $table->string('teethCount')->nullable();
            $table->string('connected')->nullable();
            $table->string('separate')->nullable();
            $table->string('notes')->nullable();
            $table->date('submitted_at');
            $table->enum('status',['Pending','Accepted','Denied','Done'])->default('Pending');
            $table->string('stage')->nullable();
            $table->foreignIdFor(Doctor::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
