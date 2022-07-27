<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            // Default Properties
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');   
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');

            $table->string('title');
            $table->string('memorandum_file_directory')->nullable();
            $table->string('location');
            $table->longText('description')->nullable();
            $table->longText('agenda')->nullable();
            $table->string('status');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            //Addditional column
            $table->boolean('is_required')->nullable();

            $table->foreignUuid('activity_type_id')->nullable();
            $table->foreign('activity_type_id')->references('id')->on('activity_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
