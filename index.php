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

$request_access_array = [
	"admin",
	"install",
	"xhr-install",
	"magic",
	"xhr-magic",
	"xhr-login",
	"xhr-logout",
	"xhr-account",
	"xhr-add",
	"xhr-update",
	"json",
	"rss", ];
if (!(in_array($request_access, $request_access_array))): $request_access = null; endif;

// No need to even connect to SQL
if (($request_access == "admin") || empty($request_access)):

	// Get JSON

	if ($request_access == "admin"):

		amp_header("Admin");

		if (array_key_exists($_REQUEST['episode'], $episodes_array)):

			// Go ahead and provide the edit for just the podcast
	
			endif;

		// Add podcast

		// View podcasts

		// 

		// 
		// Change password

		amp_footer(); endif;

	amp_header($title);
	echo "<h1>". $title ."</h1>";
	echo "<p>". $description . "</p>";
	echo "<p>RSS feed: https://". $domain ."/?access=rss</p>";
	echo "List of amp-audio for each episode";
	amp_footer();

	endif;

// Create postgres connection
$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

// Create tables and check if there is a user yet
if ($request_access == "install"):

	$result = file_get_contents("/?access=logout");

	amp_header("Install");

	// Users table
	$tables_array['users'] = [
		"user_id"		=> "VARCHAR(100)", // Fixed user id
		"username"		=> "VARCHAR(200)", // User-changeable username
		"password_salt"		=> "VARCHAR(200)", // Salt for hashing password
		"password_hash"		=> "VARCHAR(200)", // Hash of password
		"login_code"		=> "VARCHAR(200)", // Unique cookie code for login
		"login_expiration"	=> "VARCHAR(200)", // Unique cookie code for login
		"magic_code"		=> "VARCHAR(200)", // Magic code for ephemeral login
		"magic_expiration"	=> "VARCHAR(200)", // Magic code expiration time
		"authenticator_key"	=> "VARCHAR(200)", // Authenticator configuration key key
		"cookie_code"		=> "VARCHAR(200)", // Authenticator configuration key key
		];

	$tables_array['description'] = [
		"description_key"	=> "VARCHAR(100)", // title, author, description, language
		"description_info"	=> "VARCHAR(500)",
		];

	$tables_array['episodes'] = [
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

	// Check if there are users
	while ($row = pg_fetch_row($result)) { amp_footer(); }

	// Form for making new user if none exist
	echo "<form src='/?access=xhr-install' id='install-form' method='post' on='submit:install-form-submit.hide;submit-error:install-form-submit.show'>";
	
	echo "<span class='form-description'>Enter your username (must be six or more characters).</span>";
	echo "<input type='text' name='username' placeholder='Username' required>";

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

	// if there is a user then give error
	// Pull up users if empty
	$sql_temp = "SELECT * FROM users";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): json_result($domain, "error", null, "Failed accessing 'users' table."); endif;

	// Check if there are users
	while ($row = pg_fetch_row($result)) { json_result($domain, "error", null, "Users already exit."); }

	// Sanitize the username
	$_POST['username'] = trim($_POST['username']);
	if (strlen($_POST['username']) < 6): json_result($domain, "error", null, "Username too short."); endif;
	if (strlen($_POST['username']) > 50): json_result($domain, "error", null, "Username too long."); endif;

	// Prepare the values for a new user
	$magic_code = random_code(30);	
	$values_temp = [
		"user_id" 		=> random_code(16),
		"username"		=> $_POST['username'],
		"magic_code"		=> $magic_code,
		"magic_expiration"	=> time() + 300, // Expires in five minutes
		];
	
	// Prepare the statement
	$postgres_statement = postgres_statement("users", $values_temp);
	$result = pg_prepare($postgres_connection, "add_user_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare statement."); endif;
	
	// Execute the statement, make the user
	$result = pg_execute($postgres_connection, "add_user_statement", $values_temp);
	if (!($result)): json_result($domain, "error", null, "Could not add usern."); endif;

	// Redirect to magic area
	json_result($domain, "success", "/?access=magic&magic=".$magic_code, "Created new user.");

	endif;

// If there is a magic code then check it out
if ($request_access == "magic"):

	$result = file_get_contents("/?access=logout");

	// Check if magic code is right by time and match
	
	// $request_magic

	// Reset password inputs

	endif;
	
// If there is a magic code then check it out
if ($request_access == "xhr-magic"):

	$result = file_get_contents("/?access=logout");

	// Reset password
	
	// Redirect to homepage

	endif;

// Give us the login xhr
if ($request_access == "xhr-login"):

	$result = file_get_contents("/?access=logout");

	$login = null;

	// If there is a username and pwd sent, then check it
	
	// elsef there is a cookie set, then check it
	
	// $_COOKIE['loggedin_domains'];
	
	// setcookie("loggedin_domains", json_encode($loggedin_domains), (time()+24*60*60), '/');	

	// json_status("success", "Successful login. <a href='/?action=edit&domain=".$_POST['domain']."' >Continue.</a>", "/?action=edit&domain=".$_POST['domain']);
	
	// Echo out the login status

	exit; endif;

// Give us the logout xhr
if ($request_access == "xhr-logout"):

	setcookie("login_code", null, (time()+24*60*60), '/');

	json_result($domain, "success", null, "Successfully logged out.");

	exit; endif;
	
// Check the database
// Check the login status

// Give us the account xhr
if ($request_access == "xhr-account"):

	// View podcasts

	exit; endif;

// Give us the add xhr
if ($request_access == "xhr-add"):

	// View podcasts

	exit; endif;

// Give us the update xhr
if ($request_access == "xhr-update"):

	// View podcasts

	exit; endif;

// Give us the JSON
if ($request_access == "api-json"):

	// JSON of all the episodes
	// Include login status

	endif;
	
// Give us the episode
if (($request_access == "api-json") && array_key_exists($request_episode, $episode_array)):

	// Return the audio files
	// Include login status

	endif;

// Give us the RSS
if ($request_access == "api-rss"):

	// 

	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<rss version="2.0" xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">';
	echo '<channel>';

	echo '<title>' . $title .'</title>';
	echo '<googleplay:author>'. $author .'</googleplay:author>';
	echo '<description>'. $description .'</description>';
	echo '<googleplay:image href="http://www.example.com/podcasts/dafnas-zebras/img/dafna-zebra-pod-logo.jpg"/>';
	echo '<language>'. $language .'</language>';
	echo '<link>'. $link .'</link>';

		echo '<item>
		<title>Top 10 myths about caring for a zebra</title>
		<description>Here are the top 10 misunderstandings about the care, feeding, and breeding of these lovable striped animals.</description>
		<pubDate>Tue, 14 Mar 2017 12:00:00 GMT</pubDate>
		<enclosure url="https://www.example.com/podcasts/dafnas-zebras/audio/toptenmyths.mp3" type="audio/mpeg" length="34216300"/>
		<itunes:duration>30:00</itunes:duration>
		<guid isPermaLink="false">dzpodtop10</guid>
   		</item>';

	echo '</channel></rss>';

	exit; endif; ?>
