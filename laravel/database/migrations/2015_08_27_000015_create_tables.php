<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('firstname');
			$table->string('middlename')->nullable();
			$table->string('lastname');
			$table->string('suffix')->nullable();
			$table->string('gender')->nullable();
			$table->string('username')->unique;
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->integer('address')->nullable();
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->string('profile')->nullable();
			$table->string('type')->nullable();
			$table->integer('vehicle')->nullable();
			$table->integer('rating')->nullable();
			$table->integer('reviews')->nullable();
			$table->tinyInteger('stripe_active')->default(0);
			$table->string('stripe_id')->nullable();
			$table->string('stripe_subscription')->nullable();
			$table->string('stripe_plan', 100)->nullable();
			$table->string('last_four', 4)->nullable();
			$table->timestamp('trial_ends_at')->nullable();
			$table->timestamp('subscription_ends_at')->nullable();
			$table->string('access_token')->nullable();
			$table->rememberToken();
			$table->boolean('confirmed')->default(0);
			$table->string('confirmation_code')->nullable();
			$table->timestamps();
		});

		Schema::create('vehicles', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('vehicle_photos')->nullable();
			$table->string('model');
			$table->string('type');
			$table->string('year');
			$table->string('length');
			$table->integer('details')->nullable();
			$table->integer('features')->nullable();
			$table->string('description')->nullable();
			$table->integer('options')->nullable();
			$table->integer('vehicle_address')->nullable();
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->float('daily_rate');
			$table->boolean('available')->nullable();
			$table->timestamps();
		});

		Schema::create('users_address', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('street');
			$table->string('city');
			$table->string('state');
			$table->integer('zipcode');
			$table->timestamps();
		});

		Schema::create('vehicle_photos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('vehicle_id')->unsigned();
			$table->foreign('vehicle_id')->references('id')->on('vehicles');
			$table->string('url');
			$table->string('image_name');
			$table->timestamps();
		});

		Schema::create('vehicle_address', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('vehicle_id')->unsigned();
			$table->foreign('vehicle_id')->references('id')->on('vehicles');
			$table->string('street');
			$table->string('city');
			$table->string('state');
			$table->integer('zipcode');
			$table->timestamps();
		});

		Schema::create('user_reviews', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('review');
			$table->float('rating');
			$table->integer('reviewer')->unsigned();
			$table->integer('reviewee')->unsigned();
			$table->foreign('reviewer')->references('id')->on('users');
			$table->foreign('reviewee')->references('id')->on('users');
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
		Schema::drop('users');	
		Schema::drop('vehicles');
		Schema::drop('users_address');
		Schema::drop('vehicle_photos');
		Schema::drop('vehicle_address');
		Schema::drop('user_reviews');
	}

}