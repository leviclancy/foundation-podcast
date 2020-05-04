<? // Make sure the URL is correct
if (!(in_array($request_access, ["install", "xhr-install"]))): json_result($domain, "error", "/", "Invalid URL."); endif;

// Create postgres connection
$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

// XHR to create initial admin
if ($request_access == "install"):

	// First log out of any session
	$result = file_get_contents("/?access=xhr-logout");

	amp_header("Install");

	// Admins table
	$tables_array['podcast_admins'] = [
		"admin_id"		=> "VARCHAR(100)", // Fixed admin id
		"admin_name"		=> "VARCHAR(200)", // Changeable admin name
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

	// Pull up admin if empty
	$sql_temp = "SELECT * FROM podcast_admins WHERE password_salt IS NOT NULL";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): echo "<p>Error accessing 'podcast_admins' table.</p>"; endif;

	// Check if there are admins, and if there are any then our work is done
	while ($row = pg_fetch_row($result)) { amp_footer(); }

	// Form for making new admin if none exist
	echo "<form src='/?access=xhr-install' id='install-form' method='post' on='submit:install-form-submit.hide;submit-error:install-form-submit.show'>";
	
	echo "<span class='form-description'>Enter your admin name (must be six or more characters).</span>";
	echo "<input type='text' name='admin_name' minlength='6' maxlength='50' placeholder='Admin name' required>";

	echo "<span class='form-description'>Enter your password (must be six or more characters).</span>";
	echo "<input type='password' name='password' minlength='6' maxlength='50' placeholder='Password' required>";

	echo "<span class='form-submit-button' id='install-form-submit' role='button' tabindex='0' on='tap:install-form.submit'>Create admin</span>";

	echo "<div class='form-warning'>";
		echo "<div submitting>Submitting...</div>";
		echo "<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
		echo "<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>";
		echo "</div>";
		
	echo "</form>";

	amp_footer();

	endif;

// XHR to create initial admin
if ($request_access == "xhr-install"):

	$result = file_get_contents("/?access=logout");

	// We will check if any admins already exist
	$sql_temp = "SELECT * FROM podcast_admins";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Failed accessing 'podcast_admins' table."); endif;

	// If any admins already exist, then you cannot create a new one this way
	while ($row = pg_fetch_row($result)) { json_result($domain, "error", null, "Admins already exist."); }

	// Sanitize the admin name
	$_POST['admin_name'] = trim($_POST['admin_name']);
	if (strlen($_POST['admin_name']) < 6): json_result($domain, "error", null, "Admin name too short."); endif;
	if (strlen($_POST['admin_name']) > 50): json_result($domain, "error", null, "Admin name too long."); endif;

	// Sanitize the password
	$_POST['admin_name'] = trim($_POST['admin_name']);
	if (strlen($_POST['password']) < 12): json_result($domain, "error", null, "Admin name too short."); endif;
	if (strlen($_POST['password']) > 50): json_result($domain, "error", null, "Admin nam too long."); endif;

	// Prepare the values for a new admin
	$password_salt = random_code(30);	
	$values_temp = [
		"admin_id" 		=> random_code(16),
		"admin_name"		=> $_POST['admin_name'],
		"password_salt"		=> $password_salt,
		"password_hash"		=> sha1($password_salt.$_POST['password']),
		];
	
	// Prepare the statement
	$postgres_statement = postgres_update_statement("podcast_admins", $values_temp);
	$result = pg_prepare($postgres_connection, "add_admin_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare statement."); endif;
	
	// Execute the statement, make the admin
	$result = pg_execute($postgres_connection, "add_admin_statement", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not add admin."); endif;

	// Redirect to magic area
	json_result($domain, "success", "/", "Created new admin.");

	endif; ?>
