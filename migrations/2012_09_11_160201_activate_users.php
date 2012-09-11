<?php

class Sentry_Activate_Users {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Activate all existing users
		$users = User::all();
		$metadata = array();
		foreach ($users as $user) {
			$user->activated = 1;
			$user->status = 1;
			$user->save();

			$metadata[] = array(
				'user_id' => $user->id,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
			);
		}
		
		DB::table('users_metadata')->insert($metadata);

        Schema::table('users', function($table) {
        	$table->drop_column(array('first_name', 'last_name', 'middle_name', 'role'));
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}