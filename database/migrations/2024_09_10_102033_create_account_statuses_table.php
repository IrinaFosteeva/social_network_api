<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountStatusesTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('account_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status')->unique(); // Статус аккаунта
            $table->timestamps();
        });

        DB::table('account_statuses')->insert([
            ['status' => 'active'],
            ['status' => 'blocked'],
            ['status' => 'deleted'],
            ['status' => 'pending'],
            ['status' => 'suspended'],
            ['status' => 'inactive'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('account_statuses');
    }
}
