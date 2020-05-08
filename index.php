<? // Podcast has three requirements https://developers.google.com/search/docs/guides/podcast-guidelines
	// Podcast must have a homepage https://developers.google.com/search/reference/podcast/homepage-requirements
	// Podcast must have an RSS feed https://developers.google.com/search/reference/podcast/rss-feed
	// Podcast must have at least one episode https://developers.google.com/search/reference/podcast/rss-feed#episode-level

session_start();
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include_once('configuration.php');
include_once('functions.php');

// These are passed in the URL
$request_access = ( empty($_REQUEST['access']) ? null : $_REQUEST['access'] );
$request_episode = ( empty($_REQUEST['episode']) ? null : $_REQUEST['episode'] );
$request_magic = ( empty($_REQUEST['magic']) ? null : $_REQUEST['magic'] );

if (in_array($request_access, ["install", "xhr-install"])): include_once('install.php'); endif; 

// These are the possible options
$request_access_array = [
	"rss",
	"interface",
	"magic",
	"json-page",
	"json-login",
	"json-users",
	"podcast-file",
	"xhr-login",
	"xhr-logout",
	"xhr-edit-information",
	"xhr-account",
	"xhr-add",
	"xhr-update",
	];
if (!(in_array($request_access, $request_access_array))): $request_access = "interface"; endif;

// No need to connect to SQL for the interface
if ($request_access == "interface"): include_once('interface.php'); endif;

// No need to connect to SQL for the RSS, either
if ($request_access == "interface"): include_once('rss.php'); endif;

// Everything from here on requires a SQL connection
$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

// Give us the JSON of entire site info
if ($request_access == "json-page"):

	$json_array = [
		"information" => ["author"=>null, "title"=>null, "description"=>null, "language"=>null, ],
		"episodes" => [],
		];
	
	// Pull up podcast description
	$sql_temp = "SELECT information_key, information_value FROM podcast_information";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Error accessing 'podcast_information' table."); endif;

	// Check if there are episodes
	while ($row = pg_fetch_row($result)):

print_r($row);

		// Only allow existing keys
		if (!(array_key_exists($row['information_key'], $json_array['information']))): continue; endif;

		// Set up the JSON array
		$json_array['information'][$row['information_key']] = $row['information_value'];

		endwhile;

	// Pull up episodes if empty
	$sql_temp = "SELECT episode_id, episode_title, episode_description, episode_pubdate, episode_duration FROM podcast_episodes";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Error accessing 'podcast_episodes' table."); endif;

	// Check if there are episodes
	while ($row = pg_fetch_row($result)):
		
		endwhile;

	json_output($json_array);

	endif;
	
// Give us the episode
if ($request_access == "podcast-file"):

	// if there is a podcast file specified

	// Return the audio file

	endif;

// JSON just of users
if ($request_access == "json-users"):

	login_check(false); // Check login status

	// JSON of only users' admin_id, admin_name

	endif;

// If there is a magic code then check it out
if ($request_access == "magic"):

	$result = file_get_contents("https://".$domain."/?access=xhr-logout");

	// Check if magic code is right by time and match
	
	// $request_magic

	// Reset password inputs

	endif;

// Return the login details
if ($request_access == "json-login"):

	$result_temp = login_check(true);

	json_output($result_temp);

	endif;  

// Give us the login xhr
if ($request_access == "xhr-login"):

	$result = file_get_contents("https://".$domain."/?access=xhr-logout");

	$login = null;

	if (empty($_POST['login-form-admin-name'])): json_result($domain, "error", null, "No admin name."); endif;
	if (empty($_POST['login-form-password'])): json_result($domain, "error", null, "No password."); endif;

	$postgres_statement = "SELECT admin_id, password_salt, password_hash FROM podcast_admins WHERE admin_name=$1";
	$result = pg_prepare($postgres_connection, "get_admin_password_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare password search statement."); endif;

	$result = pg_execute($postgres_connection, "get_admin_password_statement", [ $_POST['login-form-admin-name'] ]);
	if (!($result)): json_result($domain, "error", null, "No result for admin name."); endif;

	$admin_id_temp = null;
	while ($row_temp = pg_fetch_assoc($result)):

		if (sha1($row_temp['password_salt'].$_POST['login-form-password']) !== $row_temp['password_hash']):
			json_result($domain, "error", null, "Password incorrect.");
			endif;

		$admin_id_temp = $row_temp['admin_id'];

		endwhile;

	if (empty($admin_id_temp)): json_result($domain, "error", null, "Could not find admin name."); endif;

	// We will start by making the cookie code and its expiration time
	$cookie_code_temp = random_code(64);
	$cookie_expiration_temp = (time()+24*60*60);

	// We will set up the values we need to update
	$values_temp = [
		"code_string"		=> $cookie_code_temp,
		"code_admin" 		=> $admin_id_temp,
		"code_type"		=> "cookie",
		"code_created"		=> time(),
		"code_expiration"	=> $cookie_expiration_temp,
		];

	// Prepare the statement to add the cookie code to SQL
	$postgres_statement = postgres_update_statement("podcast_admin_codes", $values_temp);
	$result = pg_prepare($postgres_connection, "admin_cookie_codes_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare code statement."); endif;
	
	// Execute the statement, add in the cookie
	$result = pg_execute($postgres_connection, "admin_cookie_codes_statement", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not save cookie in system."); endif;

	// Set cookie
	try { setcookie("cookie_code", $cookie_code_temp, $cookie_expiration_temp); }
	catch (Exception $exception_temp) { json_result($domain, "error", null, "Could not set cookie: ".$exception_temp->getMessage()); }

	// At this point, we are sure we are logged in
	json_result($domain, "success", null, "Successful login.");

	exit; endif;

// Give us the logout xhr
if ($request_access == "xhr-logout"):

	if (empty($_COOKIE['cookie_code'])): json_result($domain, "success", null, "Already logged out."); endif;

	// We will set up the values we need to update
	$values_temp = [
		"code_string"		=> $_COOKIE['cookie_code'],
		"code_status"		=> "deactivated",
		];

	// Prepare the statement to add the cookie code to SQL
	$postgres_statement = postgres_update_statement("podcast_admin_codes", $values_temp);
	$result = pg_prepare($postgres_connection, "admin_cookie_codes_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare code statement."); endif;
	
	// Execute the statement, add in the cookie
	$result = pg_execute($postgres_connection, "admin_cookie_codes_statement", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not deactivate cookie in system."); endif;

	try { setcookie("cookie_code", null, 1); }
	catch (Exception $exception_temp) { json_result($domain, "error", null, "Could not clear cookie: ".$exception_temp->getMessage()); }

	json_result($domain, "success", null, "Successfully logged out.");

	exit; endif;


// Give us the podcast description xhr
if ($request_access == "xhr-edit-information"):

	login_check(false); // Check login status

	$allowed_information = [
		"title",
		"author",
		"description",
		"language",
		];

	// We will set up the values we need to update
	$values_temp = [
		"information_key"		=> null,
		"information_value"		=> null,
		];

	// Prepare the statement to add the cookie code to SQL
	$postgres_statement = postgres_update_statement("podcast_information", $values_temp);
	$result = pg_prepare($postgres_connection, "podcast_information_update", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare information statement."); endif;

	$count_temp = 0; $error_temp = 0;
	foreach ($_POST as $key_temp => $value_temp):

		// Only use allowed information keys
		if (!(in_array(str_replace("edit-information-", null, $key_temp), $allowed_information))): continue; endif;

		if (empty($value_temp)): continue; endif;

		$values_temp = [
			"information_key"		=> $key_temp,
			"information_value"		=> $value_temp,
			];

		// Execute the statement, add in the cookie
		$result = pg_execute($postgres_connection, "podcast_information_update", $values_temp);
		if (!($result)): $error_temp++; endif;

		$count_temp++;

		endforeach;

	if (empty($count_temp)): json_result($domain, "error", null, "No information sent to update."); endif;
	if (!(empty($error_temp))): json_result($domain, "error", null, "Could not completely save information."); endif;

	json_result($domain, "success", null, "Saved all information.".implode($_POST));

	exit; endif;

// Give us the account xhr
if ($request_access == "xhr-account"):

	login_check(false); // Check login status

	// View podcasts

	exit; endif;

// Give us the add xhr
if ($request_access == "xhr-add"):

	login_check(false); // Check login status

	// View podcasts

	exit; endif;

// Give us the update xhr
if ($request_access == "xhr-update"):

	login_check(false); // Check login status

	// View podcasts

	exit; endif; ?>
