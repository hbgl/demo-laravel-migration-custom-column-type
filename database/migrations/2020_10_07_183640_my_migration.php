<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

class MyMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // The macros should get moved into a service provider but
        // have been put into this migration for the sake of brevity.

        Grammar::macro('typeRaw', function (Fluent $column) {
            return $column->get('raw_type');
        });

        Blueprint::macro('addColumnRaw', function ($rawType, $name) {
            /** @var \Illuminate\Database\Schema\Blueprint $this */
            return $this->addColumn('raw', $name, ['raw_type' => $rawType]);
        });
        
        DB::unprepared("CREATE TYPE foo AS ENUM ('a', 'b', 'c');");
        DB::unprepared("CREATE TYPE bar AS ENUM ('x', 'y', 'z');");
        
        Schema::create('my_table', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->addColumnRaw('foo', 'my_foo');
            $table->addColumnRaw('bar', 'my_bar');
            $table->addColumnRaw('foo[]', 'my_multiple_foos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_table');

        DB::unprepared('DROP TYPE bar;');
        DB::unprepared('DROP TYPE foo;');
    }
}
