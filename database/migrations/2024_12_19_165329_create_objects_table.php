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
        Schema::create('objects', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->longText('value'); // BLOB or TEXT https://dev.mysql.com/doc/refman/8.4/en/blob.html
            $table->boolean('is_binary')->default(false);
            $table->integer('timestamp');
            $table->timestamps();

            $table->index(['key', 'timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objects');
    }
};
