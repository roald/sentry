<?php

class Sentry_Activate_Users {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        // Password requires at least 80 characters for Sentry's hashing function
        DB::query("ALTER TABLE  `users` CHANGE  `password`  `password` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		// Activate all existing users
		$users = User::all();
		$metadata = array();
		foreach ($users as $user) {
			$user->activated = 1;
			$user->status = 1;

			$salt = Str::random(16);
			$password = "idd-". $user->username;
			$password = $salt . hash('sha256', $salt.$password);
			$user->password = $password;

			$user->save();

			$metadata[] = array(
				'user_id' => $user->id,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
			);
		}
		
		DB::table('users_metadata')->insert($metadata);

        Schema::table('users', function($table) {
        	$table->drop_column(array('first_name', 'last_name', 'middle_name', 'role', 'active'));
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