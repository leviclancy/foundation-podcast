<? // Create postgres connection
$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

// Create tables and check if there is a user yet
if ($request_access == "install"):

	$result = file_get_contents("/?access=logout");

	amp_header("Install");

	// Users table
	$tables_array['podcast_admins'] = [
		"admin_id"		=> "VARCHAR(100)", // Fixed user id
		"admin_name"		=> "VARCHAR(200)", // User-changeable username
		"password_salt"		=> "VARCHAR(200)", // Unique salt for password
		"password_hash"		=> "VARCHAR(200)", // Hash of password
		"authenticator_key"	=> "VARCHAR(200)", // Authenticator configuration key
		"magic_code"		=> "VARCHAR(200)", // Magic code for ephemeral login
		"magic_expiration"	=> "VARCHAR(200)", // Magic code expiration time
		"cookie_codes"		=> "TEXT", // JSON with cookie codes and their expiration dates, for login
		];

	$tables_array['podcast_description'] = [
		"description_key"	=> "VARCHAR(100)", // title, author, description, language
		"description_info"	=> "VARCHAR(500)",
		];

	$tables_array['podcast_episodes'] = [
		"episode_id"		=> "VARCHAR(100)",
		"episode_title"		=> "VARCHAR(200)",
		"episode_description"	=> "VARCHAR(500)",
		"episode_pubdate"	=> "VARCHAR(100)",
		"episode_duration"	=> "VARCHAR(100)",
		"episode_file"		=> "TEXT",
		];
		
	// Start generating the tables...
	foreach($tables_array as $table_name => $columns_array):

		// Prepare request...
		$columns_schema = [];
		foreach ($columns_array as $column_name => $column_type):
			$columns_schema[] = $column_name." ".$column_type;
			endforeach;
		$columns_schema[0] .= " PRIMARY KEY";

		// Execute query...
		$sql_temp = "CREATE TABLE IF NOT EXISTS $table_name (". implode (", ", $columns_schema) .")";
		$result = pg_query($postgres_connection, $sql_temp);
		
		// If it failed or succeeded...
		$result_temp = (empty($result) ? "Error" : "Success");
		echo "<p>". $result_temp . " building table '". $table_name ."' in database '". $sql_database ."'.</p>";

		endforeach;

	// Pull up users if empty
	$sql_temp = "SELECT * FROM users WHERE password_salt IS NOT NULL";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): echo "<p>Error accessing 'users' table.</p>"; endif;

	// Check if there are users, and if there are any then our work is done
	while ($row = pg_fetch_row($result)) { amp_footer(); }

	// Form for making new user if none exist
	echo "<form src='/?access=xhr-install' id='install-form' method='post' on='submit:install-form-submit.hide;submit-error:install-form-submit.show'>";
	
	echo "<span class='form-description'>Enter your username (must be six or more characters).</span>";
	echo "<input type='text' name='username' placeholder='Username' required>";

	echo "<span class='form-description'>Enter your password (must be six or more characters).</span>";
	echo "<input type='password' name='password' placeholder='Password' required>";

	echo "<span class='form-submit-button' id='install-form-submit' role='button' tabindex='0' on='tap:install-form.submit'>Create user</span>";

	echo "<div class='form-warning'>";
		echo "<div submitting>Submitting...</div>";
		echo "<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
		echo "<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>";
		echo "</div>";
		
	echo "</form>";

	amp_footer();

	endif;

// XHR to create initial user
if ($request_access == "xhr-install"):

	$result = file_get_contents("/?access=logout");

	// We will check if any users already exist
	$sql_temp = "SELECT * FROM users";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Failed accessing 'users' table."); endif;

	// If any users already exist, then you cannot create a new one this way
	while ($row = pg_fetch_row($result)) { json_result($domain, "error", null, "Users already exist."); }

	// Sanitize the username
	$_POST['username'] = trim($_POST['username']);
	if (strlen($_POST['username']) < 6): json_result($domain, "error", null, "Username too short."); endif;
	if (strlen($_POST['username']) > 50): json_result($domain, "error", null, "Username too long."); endif;

	// Sanitize the password
	$_POST['username'] = trim($_POST['username']);
	if (strlen($_POST['password']) < 12): json_result($domain, "error", null, "Username too short."); endif;
	if (strlen($_POST['password']) > 50): json_result($domain, "error", null, "Username too long."); endif;

	// Prepare the values for a new user
	$password_salt = random_code(30);	
	$values_temp = [
		"user_id" 		=> random_code(16),
		"username"		=> $_POST['username'],
		"password_salt"		=> $password_salt,
		"password_hash"		=> sha1($password_salt.$_POST['password']),
		];
	
	// Prepare the statement
	$postgres_statement = postgres_update_statement("users", $values_temp);
	$result = pg_prepare($postgres_connection, "add_user_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare statement."); endif;
	
	// Execute the statement, make the user
	$result = pg_execute($postgres_connection, "add_user_statement", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not add user."); endif;

	// Redirect to magic area
	json_result($domain, "success", "/", "Created new user.");

	endif; ?>
