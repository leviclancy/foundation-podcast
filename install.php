<? // Make sure the URL is correct
if (!(in_array($request_access, ["install", "xhr-install"]))): json_result($domain, "error", "/", "Invalid URL."); endif;

// Create postgres connection
$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

// XHR to create initial admin
if ($request_access == "install"):

	// First log out of any session
	$result = file_get_contents("/?access=xhr-logout");

	echo "<!doctype html><html amp lang='en'>";
	
	echo "<head><meta charset='utf-8'>";
	
	echo "<script async src='https://cdn.ampproject.org/v0.js'></script>";

	echo "<link rel='canonical' href='https://". $domain ."/?access=install'>";

	echo "<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>";

	echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';
	echo '<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>';
	echo '<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>';

	echo "<title>Install</title>";

	echo "<meta name='theme-color' content='#2878b4'>";

	echo "<meta name='viewport' content='width=device-width,minimum-scale=1,initial-scale=1'>";

	$style_array = [
		"body, input" => [
			"font-family" 	=> "Verdana",
			"font-size"	=> "14px",
			],
		
		"#body-wrapper" => [
			"padding"	=> "20px",
			"margin"	=> "0 auto",
			"text-align"	=> "left",
			"width"		=> "100%",
			"max-width"	=> "600px",
			"box-sizing"	=> "border-box",
			],
		
		"p, .install-form-label, .install-form-input" => [
			"text-align"	=> "left",
			"box-sizing"	=> "border-box",
			"width" 	=> "90%",
			"display"	=> "block",
			"clear"		=> "both",
			"font-size"	=> "100%",
			"line-height"	=> "1.3em",
			"background"	=> "none",
			],
		
		"p" => [
			"padding" 	=> "20px 0 20px 20px",
			"margin"	=> "20px 0",
			],
		
		".install-form-label" => [
			"font-size"	=> "80%",
			"font-style"	=> "italic",
			"margin"	=> "40px 0 0",
			"padding"	=> "20px 0 0 20px",
			],
		
		".install-form-input" => [
			"font-size"	=> "14px",
			"border-width" 	=> "2px",
			"border-style"	=> "solid",
			"border-color"	=> "#555 #bbb #bbb #555",
			"border-radius" => "100px",
			"color"		=> "#333",
			"padding"	=> "20px",
			"margin"	=> "20px 0",
			],
		
		".form-warning" => [
			"margin"	=> "30px 0",
			"padding"	=> "20px 0 0 20px",
			"font-style"	=> "italic",
			"font-size"	=> "90%",
			"text-align"	=> "center",
			],
		
		"#install-form-submit" => [
			"display"	=> "table",
			"clear"		=> "both",
			"margin"	=> "40px auto",
			"border-radius"	=> "100px",
			"background"	=> "#333",
			"padding"	=> "20px 30px",
			"color"		=> "#fff",
			"text-align"	=> "center",
			"cursor"	=> "pointer",
			],
		
		":-webkit-autofill" => [
			"font-size"	=> "100%",
			],
		
		"::placeholder"	=> [
			"font-size"	=> "100%",
			"color"		=> "#777",
			],
		
		"*:focus" => [
			"outline"	=> "none",
			"outline-width"	=> "none",
			],

		];

	echo "<style amp-custom>" . css_output($style_array);
//	echo " @media only screen and (max-width: 600px)  { p, input, label { margin: 20px; } }";
	echo "</style>";

	echo "</head><body>";

	// Admins table
	$tables_array['podcast_admins'] = [
		"admin_id"		=> "VARCHAR(100)", // Fixed admin id
		"admin_name"		=> "VARCHAR(200)", // Changeable admin name
		"password_salt"		=> "VARCHAR(200)", // Unique salt for password
		"password_hash"		=> "VARCHAR(200)", // Hash of password
		"authenticator_key"	=> "VARCHAR(200)", // Authenticator configuration key
		];

	// Cookies table
	$tables_array['podcast_admin_codes'] = [
		"code_string"		=> "VARCHAR(100)", // Unique code id, should be 64 characters
		"code_admin"		=> "VARCHAR(100)", // The admin id it refers to		
		"code_type"		=> "VARCHAR(100)", // Either 'cookie' or 'magic'
		"code_status"		=> "VARCHAR(100)", // Either empty or 'deactivated'
		"code_created"		=> "VARCHAR(100)", // Its creation as a UNIX timestamp
		"code_expiration"	=> "VARCHAR(100)", // Its expiration as a UNIX timestamp
		];

	$tables_array['podcast_information'] = [
		"information_key"	=> "VARCHAR(100)", // Title, author, description, language, email, image
		"information_value"	=> "TEXT", // Must be TEXT for the podcast image
		];

	$tables_array['podcast_episodes'] = [
		"episode_id"		=> "VARCHAR(100)",
		"episode_title"		=> "VARCHAR(200)",
		"episode_description"	=> "VARCHAR(500)",
		"episode_pubdate"	=> "VARCHAR(100)",
		"episode_duration"	=> "VARCHAR(100)",
		"episode_status"	=> "VARCHAR(100)",
		"episode_file"		=> "TEXT",
		"episode_bytes_length"	=> "VARCHAR(100)",
		];

	echo "<div id='body-wrapper'>";

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
		echo "<p>" . $result_temp . " building <i>". $table_name ."</i> in <i>". $sql_database ."</i>.</p>";

		endforeach;

	// Pull up admin if empty
	$sql_temp = "SELECT * FROM podcast_admins WHERE password_salt IS NOT NULL";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): echo "<p>Error accessing 'podcast_admins' table.</p>"; endif;

	// Check if there are admins, and if there are any then our work is done
	while ($row = pg_fetch_row($result)): echo "</div>"; amp_footer(); endwhile;

	// Create magic code to set username and password
	// TAP TO CONTINUE SETUP

	// Form for making new admin if none exist
	echo "<form action-xhr='/?access=xhr-install' target='_top' id='install-form' method='post' on='submit:install-form-submit.hide;submit-error:install-form-submit.show'>";
	
	echo "<label class='install-form-label' for='install-form-admin-name'>Enter your admin name (must be six or more characters).</label>";
	echo "<input class='install-form-input' type='text' id='admin_name' name='install-form-admin-name' minlength='6' maxlength='50' placeholder='Admin name' required>";

	echo "<label class='install-form-label' for='install-form-password'>Enter your password (must be six or more characters).</label>";
	echo "<input class='install-form-input' type='password' id='password' name='install-form-password' minlength='6' maxlength='50' placeholder='Password' required>";

	echo "<div class='form-warning'>";
		echo "<div submitting>Submitting...</div>";
		echo "<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
		echo "<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>";
		echo "</div>";
		
	echo "<span id='install-form-submit' role='button' tabindex='0' on='tap:install-form.submit'>Create admin</span>";

	echo "</form>";

	echo "</div>"; amp_footer();

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
	$_POST['install-form-admin-name'] = trim($_POST['install-form-admin-name']);
	if (strlen($_POST['install-form-admin-name']) < 6): json_result($domain, "error", null, "Admin name too short."); endif;
	if (strlen($_POST['install-form-admin-name']) > 50): json_result($domain, "error", null, "Admin name too long."); endif;

	// Sanitize the password
	$_POST['install-form-password'] = trim($_POST['install-form-password']);
	if (strlen($_POST['install-form-password']) < 12): json_result($domain, "error", null, "Admin name too short."); endif;
	if (strlen($_POST['install-form-password']) > 50): json_result($domain, "error", null, "Admin nam too long."); endif;

	// Prepare the values for a new admin
	$password_salt = random_code(30);	
	$values_temp = [
		"admin_id" 		=> random_code(16),
		"admin_name"		=> $_POST['install-form-admin-name'],
		"password_salt"		=> $password_salt,
		"password_hash"		=> sha1($password_salt.$_POST['install-form-password']),
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
