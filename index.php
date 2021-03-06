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
	"json-admins",
	"episode-file",
	"xhr-login",
	"xhr-logout",
	"xhr-edit-information",
	"xhr-edit-episode",
	"xhr-delete-episode",
	"xhr-add-episode",
	"xhr-account",
	"xhr-add",
	"xhr-update",
	];
if (!(in_array($request_access, $request_access_array))): $request_access = "interface"; endif;

// No need to connect to SQL for the interface
if ($request_access == "interface"): include_once('interface.php'); endif;

// No need to connect to SQL for the RSS, either
if ($request_access == "rss"): include_once('rss.php'); endif;

// Everything from here on requires a SQL connection
$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

// Give us the JSON of entire site info
if ($request_access == "json-page"):

	$page_temp = $_REQUEST['page'] ?? 0;

	$json_array = [
		"information" 	=> ["author"=>"", "title"=>"", "description"=>"", "email"=>"", "language"=>"", ],
		"episodes" 	=> [],
		"login"		=> [],
		"next"		=> null,
		];
	
	$login_temp = login_check(true);

	// Pull up podcast description
	$sql_temp = "SELECT information_key, information_value FROM podcast_information";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Error accessing 'podcast_information' table."); endif;

	// Check if there are episodes
	while ($row = pg_fetch_assoc($result)):

		// Only allow existing keys
		if (!(array_key_exists($row['information_key'], $json_array['information']))): continue; endif;

		// Set up the JSON array
		$json_array['information'][$row['information_key']] = $row['information_value'];

		endwhile;

	// Pull up episodes if empty
	$sql_temp = "SELECT episode_id, episode_title, episode_description, episode_pubdate, episode_duration, episode_status FROM podcast_episodes ORDER BY episode_pubdate DESC";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Error accessing 'podcast_episodes' table."); endif;

	// This is used to count pagination
	$count_temp = 0;

	// Check if there are episodes
	$next_temp = 0; // This means there is no next
	while ($row = pg_fetch_assoc($result)):

		// Assign complete or incomplete status
		$completion_temp = episode_completion_check($row);

		// Standardize active status
		$status_temp = "inactive";
		if ($row['episode_status'] == "active"): $status_temp = "active"; endif;

		// If we are not logged in, we will skip incomplete and inactive episodes
		if ($login_temp['loginStatus'] !== "loggedin"):
			if ($status_temp !== "active"): continue; endif; // Skip inactive episodes
			if ($completion_temp !== "complete"): continue; endif; // Skip incomplete episodes
			if (strtotime($row['episode_pubdate']) > strtotime(date("Y-m-d"))): continue; endif; // Skip unpublished episodes
			endif;

		// We will use this for pagination
		$count_temp++;

		// This is for pagination
		if ($count_temp < $page_temp*50): continue; endif;
		if ($count_temp > ($page_temp+1)*50): $next_temp = 1; continue; endif;

		$json_array['episodes'][] = [
			"episode_id"		=> $row['episode_id'],
			"episode_title"		=> $row['episode_title'],
			"episode_description"	=> $row['episode_description'],
			"episode_pubdate"	=> $row['episode_pubdate'],
			"episode_pubdate_fancy"	=> date("D, jS M Y", strtotime($row['episode_pubdate'])),
//			"episode_duration"	=> $row['episode_duration'],
			"episode_bytes_length"	=> $row['episode_bytes_length'],
			"episode_status"	=> $status_temp,
			"episode_completion"	=> $completion_temp,
			];

		endwhile;

	// If we have pages to go to next... 
	if ($next_temp !== 0): $son_array['next'] = "/?access=json-page&page=".($page_temp+1); endif;

	$json_array['login'] = $login_temp;

	json_output($json_array);

	endif;
	
// Give us the episode
if ($request_access == "episode-file"):

	$episode_id_request = $_REQUEST['episode-file'] ?? null;

	if (empty($episode_id_request)): header("HTTP/1.0 404 Not Found"); exit; endif;

	$result_temp = login_check(true);

	$postgres_statement = "SELECT episode_file, episode_status FROM podcast_episodes WHERE episode_id=$1";
	$result = pg_prepare($postgres_connection, "get_episode_file_statement", $postgres_statement);
	if (!($result)): header("HTTP/1.0 404 Not Found"); exit; json_result($domain, "error", null, "Could not prepare podcast file statement."); endif;

	$result = pg_execute($postgres_connection, "get_episode_file_statement", [ $episode_id_request ]);
	if (!($result)): header("HTTP/1.0 404 Not Found"); exit; json_result($domain, "error", null, "No result for episode file."); endif;

	while ($row_temp = pg_fetch_assoc($result)):

//		if ($result_temp['loginStatus'] !== "loggedin"):
//			if (episode_completion_check($row_temp) !== "complete"): json_result($domain, "error", null, "Incomplete file."); endif;
//			if ($row_temp['episode_status'] !== "active"): json_result($domain, "error", null, "Inactive file."); endif;
//			endif;

		header('Content-Type: audio/mpeg3');
		echo base64_decode($row_temp['episode_file']);
		exit; endwhile;

	endif;

// JSON just of users
if ($request_access == "json-admins"):

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


// Give us the xhr to edit the overall podcast information
if ($request_access == "xhr-edit-information"):

	login_check(false); // Check login status

	$allowed_information = [
		"title",
		"author",
		"description",
		"email",
		"language",
		];

	// We will set up the values we need to update
	$values_temp = [
		"information_key"		=> null,
		"information_value"		=> null,
		];

	if (empty($_POST['edit-information'])): json_result($domain, "error", null, "No information array received."); endif;

	// Prepare the statement to add the cookie code to SQL
	$postgres_statement = postgres_update_statement("podcast_information", $values_temp);
	$result = pg_prepare($postgres_connection, "podcast_information_update", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare information statement."); endif;

	$count_temp = 0; $error_temp = 0;
	foreach ($allowed_information as $key_temp):

		// Only use allowed information keys
		if (!(array_key_exists($key_temp, $_POST['edit-information']))): continue; endif;

		// Skip empty information
		if (empty($_POST['edit-information'][$key_temp])): continue; endif;

		$values_temp = [
			"information_key"		=> $key_temp,
			"information_value"		=> $_POST['edit-information'][$key_temp],
			];

		// Execute the statement, add in the cookie
		$result = pg_execute($postgres_connection, "podcast_information_update", $values_temp);
		if (!($result)): $error_temp++; endif;

		$count_temp++;

		endforeach;

	if (empty($count_temp)): json_result($domain, "error", null, "No information sent to update."); endif;
	if (!(empty($error_temp))): json_result($domain, "error", null, "Could not completely save information."); endif;

	json_result($domain, "success", null, "Saved all information.");

	exit; endif;

// Give us the xhr to edit a single podcast episode
if ($request_access == "xhr-edit-episode"):

	login_check(false); // Check login status

	// If no valid post data is received
	if (empty($_POST['edit-episode'])): json_result($domain, "error", null, "No information array received."); endif;

	// Set up which values to use and look for
	$values_temp = [
		"episode_id" 		=> null,
		"episode_title" 	=> null,
		"episode_description" 	=> null,
		"episode_pubdate" 	=> null,
//		"episode_duration" 	=> null,
		];

	// Return an error if anything is null
	foreach ($values_temp as $key_temp => $value_temp):
		if (empty($_POST['edit-episode'][$key_temp])): json_result($domain, "error", null, "Value for '".$key_temp."' not received."); endif;
		$values_temp[$key_temp] = $_POST['edit-episode'][$key_temp] ?? null;
		endforeach;

	// Not essential
	$values_temp['episode_status'] = $_POST['edit-episode']['episode_status'] ?? null;

	// Prepare the statement to update the podcast episode SQL
	$postgres_statement = postgres_update_statement("podcast_episodes", $values_temp);
	$result = pg_prepare($postgres_connection, "podcast_episodes_update", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare episodes statement."); endif;

	// Execute the statement, update the episode
	$result = pg_execute($postgres_connection, "podcast_episodes_update", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not update episode."); endif;

	json_result($domain, "success", null, "Updated episode."); 

	endif;

// Give us the xhr to edit a single podcast episode
if ($request_access == "xhr-delete-episode"):

	login_check(false); // Check login status

	// If no valid post data is received
	if (empty($_POST['delete-episode'])): json_result($domain, "error", null, "No information array received."); endif;

	// Return an error if anything is null
	if (empty($_POST['delete-episode']['episode_id'])): json_result($domain, "error", null, "Episode ID not received."); endif;

	// Prepare the statement to update the podcast episode SQL
	$postgres_statement = "DELETE FROM podcast_episodes WHERE episode_id=$1";
	$result = pg_prepare($postgres_connection, "podcast_episodes_delete", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare episodes statement."); endif;

	// Execute the statement, update the episode
	$result = pg_execute($postgres_connection, "podcast_episodes_delete", [ $_POST['delete-episode']['episode_id'] ]);
	if (!($result)): json_result($domain, "error", null, "Could not delete episode."); endif;

	json_result($domain, "success", null, "Deleted episode."); 

	endif;

// Give us the xhr to edit a single podcast episode
if ($request_access == "xhr-add-episode"):

	login_check(false); // Check login status

	// If no valid post data is received
	if (empty($_FILES['add-episode'])): json_result($domain, "error", null, "No file received."); endif;

	// If no file received
	if (empty($_FILES['add-episode']['size'])): json_result($domain, "error", null, "No file received."); endif;

	// Cutoff is about 64 megabytes
	if ($_FILES['add-episode']['size'] > (64*1024*1024)): json_result($domain, "error", null, "File too large."); endif;

	// Cutoff is about 512 kilobytes
	if ($_FILES['add-episode']['size'] < (512*1024)): json_result($domain, "error", null, "File too small."); endif;

	$values_temp = [
		"episode_id" 		=> random_code(12),
		"episode_file"		=> base64_encode(file_get_contents($_FILES['add-episode']['tmp_name'])), // Original image content
		"episode_pubdate" 	=> date("Y-m-d"),
		"episode_bytes_length"	=> $_FILES['add-episode']['size'],
		];

	// Prepare the statement to update the podcast episode SQL
	$postgres_statement = postgres_update_statement("podcast_episodes", $values_temp);
	$result = pg_prepare($postgres_connection, "podcast_episodes_add", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare episode statement."); endif;

	// Execute the statement, update the episode
	$result = pg_execute($postgres_connection, "podcast_episodes_add", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not add episode."); endif;

	json_result($domain, "success", null, "Added episode."); 

	endif;

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
