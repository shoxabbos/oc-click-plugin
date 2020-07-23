<?php namespace Shohabbos\Click\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosClickTransactions extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_click_transactions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('amount');
            $table->string('account');
            $table->integer('click_trans_id');
            $table->string('status');
            $table->string('error');
            $table->integer('date');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_click_transactions');
    }
}
