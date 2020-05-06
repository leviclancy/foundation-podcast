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
	"json-login",
	"xhr-login",
	"xhr-logout",
	"xhr-account",
	"xhr-add",
	"xhr-update",
	"json-page",
	];
if (!(in_array($request_access, $request_access_array))): $request_access = "interface"; endif;

// No need to connect to SQL for the interface
if ($request_access == "interface"): include_once('interface.php'); endif;

// No need to connect to SQL for the RSS, either
if ($request_access == "interface"): include_once('rss.php'); endif;

// Everything from here on requires a SQL connection
$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

// Give us the JSON
if ($request_access == "json-page"):

	$json_array = [
		"about" => [],
		"episodes" => [],
		];
	
	// Pull up podcast description
	$sql_temp = "SELECT description_key, description_info FROM podcast_description";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Error accessing 'podcast_description' table."); endif;

	// Check if there are episodes
	while ($row = pg_fetch_row($result)):
		$json_array['about'][$row['description_key']] = $row['description_info'];	
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
if (($request_access == "json-episodes") && array_key_exists($request_episode, $episode_array)):

	// Return the audio files
	// Include login status

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

	$json_temp = [
		"loginStatus"		=> "loggedout",
		"loginMessage"		=> null,
		"loginAdminID"		=> null,
		"loginAdminName"	=> null,
		"loginExpiration"	=> null,
		];

	// If no cookie code, just ignore it
	if (empty($_COOKIE['cookie_code'])):
		$json_temp['loginMessage'] = "No cookie code.";
		json_output ($json_temp); endif;

	// Prepare cookie code lookup statement
	$postgres_statement = "SELECT admin_id, admin_name, cookie_codes FROM podcast_admins WHERE cookie_codes LIKE CONCAT('%', $1 ,'%')";
	$result = pf_prepare($postgres_connection, "get_cookie_codes_statement", $postgres_statement);
	if (!($result)):
		$json_temp['loginMessage'] = "Could not prepare statement.";
		json_output ($json_temp); endif;

	// Search for cookie code
	$result = pg_execute($postgres_connection, "get_cookie_codes_statement", [ $_COOKIE['cookie_code'] ]);
	if (!($result)):
		$json_temp['loginMessage'] = "Could not find cookie code.";
		json_output ($json_temp); endif;

	while ($row_temp = pg_fetch_assoc($result)):

		// Let's get the cookie codes array
		$cookie_codes_array = json_decode($row_temp['cookie_codes'], true);

		// Let's get the cookie code and expiration
		foreach ($cookie_codes_array as $key_temp => $cookie_code_temp):

			// If the cookie codes do not match, move on
			if ($_COOKIE['cookie_code'] !== $cookie_code_temp['cookie_code']): continue; endif;

			// We'll break, keeping the last $cookie_code_temp
			break;

			endforeach;

		// In case there were no matches, we have to double check
		if ($_COOKIE['cookie_code'] !== $cookie_code_temp['cookie_code']): continue; endif;

		// If expired cookie code
		if ($cookie_code_temp['cookie_expiration'] < time()):
			$json_temp['loginMessage'] = "Expired cookie code.";
			json_output ($json_temp); endif;

		$json_temp['loginStatus']	= 'loggedin';
		$json_temp['loginMessage']	= 'Logged in.';
		$json_temp['loginAdminID']	= $row_temp['admin_id'];
		$json_temp['loginAdminName']	= $row_temp['admin_name'];
		$json_temp['loginExpiration']	= $cookie_code_temp['cookie_expiration'];

		json_output ($json_temp);

		endwhile;

	$json_temp['loginMessage'] = "Failed to find admin.";
	json_output ($json_temp);

	endif;  

// Give us the login xhr
if ($request_access == "xhr-login"):

	$result = file_get_contents("https://".$domain."/?access=xhr-logout");

	$login = null;

	if (empty($_POST['admin_name'])): json_result($domain, "error", null, "No admin name."); endif;
	if (empty($_POST['password'])): json_result($domain, "error", null, "No password."); endif;

	$postgres_statement = "SELECT admin_id, password_salt, password_hash, cookie_codes FROM podcast_admins WHERE admin_name=$1";
	$result = pg_prepare($postgres_connection, "get_admin_password_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare statement."); endif;

	$result = pg_execute($postgres_connection, "get_admin_password_statement", [ $_POST['admin_name'] ]);
	if (!($result)): json_result($domain, "error", null, "No result for admin name."); endif;

	$admin_id_temp = null;
	while ($row_temp = pg_fetch_assoc($result)):

		if (sha1($row_temp['password_salt'].$_POST['password']) !== $row_temp['password_hash']):
			json_result($domain, "error", null, "Password incorrect.");
			endif;

		$admin_id_temp = $row['admin_id'];

		$cookie_codes_array = json_decode($row_temp['cookies_codes'], true);

		endwhile;

	if (empty($admin_id_temp)): json_result($domain, "error", null, "Could not find admin name."); endif;

	// We will start by making the cookie code and its expiration time
	$cookie_code_temp = random_code(64);
	$cookie_expiration_temp = (time()+24*60*60);

	// Then we will update the cookie codes array
	$cookie_codes_array[$cookie_expiration_temp.random_code(8)] = [
		"cookie_code" 		=> $cookie_code_temp,
		"cookie_expiration"	=> $cookie_expiration_temp,
		];

	// We'll remove expired cookies
	foreach ($cookie_codes_array as $key_temp => $cookie_code_temp):
		if ($cookie_code_temp['cookie_expiration'] > time()): continue; endif; // If expiration is in future, continue
		unset($cookie_codes_array[$key_temp]); // Otherwise, unset the cookie code
		endforeach;

	// We will sort, count, and prune it to a reasonable use limit
	krsort($cookie_codes_array);
	while (count($cookie_codes_array) > 16): $discard_temp = array_pop($cookie_codes_array); endwhile;

	// We will set up the values we need to update
	$values_temp = [
		"admin_id" 		=> $admin_id_temp,
		"cookie_codes"		=> json_encode($cookie_codes_array),
		];

	// Prepare the statement to add the cookie code to SQL
	$postgres_statement = postgres_update_statement("podcast_admins", $values_temp);
	$result = pg_prepare($postgres_connection, "admin_cookie_codes_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare statement."); endif;
	
	// Execute the statement, add in the cookie
	$result = pg_execute($postgres_connection, "admin_cookie_codes_statement", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not save cookie in system."); endif;

	// Set cookie
	setcookie("cookie_code", $cookie_code_temp, $cookie_expiration_temp, '/');
	if (!(empty($_COOKIE['cookie_code']))): json_result($domain, "error", null, "Failed to save cookie in browser."); endif;

	login_check();

	// At this point, we are sure we are logged in
	json_result($domain, "success", null, "Successful login.");

	exit; endif;

// Give us the logout xhr
if ($request_access == "xhr-logout"):

	if (empty($_COOKIE['code'])): json_result($domain, "success", null, "Already logged out."); endif;

	setcookie("cookie_code", null, (time()+24*60*60), '/');

	json_result($domain, "success", null, "Successfully logged out.");

	exit; endif;

// Give us the account xhr
if ($request_access == "xhr-account"):

	login_check();

	// View podcasts

	exit; endif;

// Give us the add xhr
if ($request_access == "xhr-add"):

	login_check();

	// View podcasts

	exit; endif;

// Give us the update xhr
if ($request_access == "xhr-update"):

	login_check();

	// View podcasts

	exit; endif; ?>
