<?php

use App\Models\Setting;
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
        foreach ([
            "app_name" => "Cardcounter",
            "metadata_title" => "Cardcounter: gry karciane",
            "metadata_author" => "Wojciech Przybyła",
        ] as $key => $value) {
            Setting::find($key)->update(["value" => $value]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
