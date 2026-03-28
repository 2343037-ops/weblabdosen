<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Jobs table not needed - using sync queue driver
    }

    public function down(): void
    {
        //
    }
};
