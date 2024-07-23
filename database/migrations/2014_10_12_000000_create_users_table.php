<?php

use App\Enums\UserGender;
use App\Enums\UserStatus;
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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('mobile', 13)->unique()->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->string('username', 100)->unique()->nullable();
            $table->enum('status', UserStatus::toArray())->default(UserStatus::ACTIVE);
            $table->enum('gender', UserGender::toArray())->default(UserGender::GENDER_MAN);
            $table->string('password', 100)->nullable();
            $table->string('avatar', 100)->nullable();
            $table->string('about_me', 250)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
