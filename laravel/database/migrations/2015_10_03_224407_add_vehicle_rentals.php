<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVehicleRentals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rentals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('renter');
			$table->integer('rentee');
			$table->integer('vehicle');
			$table->float('amount');
			$table->date('rental_start');
			$table->date('rental_end');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rentals');
	}

}
