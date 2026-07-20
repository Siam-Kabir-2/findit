<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admins')) {
            return;
        }

        Schema::create('admins', function (Blueprint $table) {
            $table->increments('admin_id');
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('category_id');
            $table->string('category_name', 100)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->increments('location_id');
            $table->string('location_name', 100)->unique();
            $table->string('description', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->increments('item_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('location_id');
            $table->string('item_name', 100);
            $table->string('item_description', 500)->nullable();
            $table->string('item_type', 10);
            $table->string('item_image', 255)->nullable();
            $table->date('lost_or_found_date');
            $table->string('status', 20)->default('PENDING');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('category_id')->references('category_id')->on('categories')->cascadeOnDelete();
            $table->foreign('location_id')->references('location_id')->on('locations')->cascadeOnDelete();
        });

        Schema::create('claims', function (Blueprint $table) {
            $table->increments('claim_id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('user_id');
            $table->string('claim_message', 500)->nullable();
            $table->string('proof_description', 500)->nullable();
            $table->string('claim_status', 20)->default('PENDING');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('item_id')->references('item_id')->on('items')->cascadeOnDelete();
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->increments('audit_id');
            $table->string('table_name', 50);
            $table->unsignedInteger('record_id');
            $table->string('action_type', 20);
            $table->string('old_status', 20)->nullable();
            $table->string('new_status', 20)->nullable();
            $table->timestamp('action_date')->useCurrent();
            $table->string('action_by', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('items');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('admins');
    }
};
