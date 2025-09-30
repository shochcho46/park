<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\District;
use App\Models\Division;
use App\Models\Profession;
use App\Models\Union;
use App\Models\Upazila;
use App\Models\User;
use App\Models\Religion;
use App\Models\EducationType;
use App\Models\Gender;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Division::class)->nullable();
            $table->foreignIdFor(District::class)->nullable();
            $table->foreignIdFor(Upazila::class)->nullable();
            $table->foreignIdFor(Union::class)->nullable();
            $table->foreignIdFor(EducationType::class)->nullable();
            $table->foreignIdFor(Profession::class)->nullable();
            $table->foreignIdFor(Gender::class);
            $table->foreignIdFor(Country::class)->nullable();
            $table->foreignIdFor(Religion::class)->nullable();
            $table->string('nid')->unique()->nullable();
            $table->string('passport')->unique()->nullable();
            $table->string('birth_certificate')->unique()->nullable();
            $table->text('full_address')->nullable();
            $table->date('dob')->nullable();
            $table->boolean('is_autism')->default(1)->comment("1:normal,0:disable")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
