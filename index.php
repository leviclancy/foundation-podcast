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

$request_access_array = [
	"admin",
	"magic",
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


// Only make an SQL connection for these endpoints,
$sql_array = [
	"magic",
	"json-login",
	"xhr-login",
	"xhr-logout",
	];
// If we are at one of these endpoints, then create Postgres connection,
if (in_array($request_access, $sql_array)):
	$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
	if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;
	endif;


// If there is a magic code then check it out
if ($request_access == "magic"):

	$result = file_get_contents("/?access=logout");

	// Check if magic code is right by time and match
	
	// $request_magic

	// Reset password inputs

	endif;

// Return the login details
if ($request_access == "json-login"):


	// If not cookie code, just ignore it
	if (empty($_COOKIE['cookie_code'])): json_output (["loginStatus" => "loggedout", "loginMessage" => "No cookie code."]); endif;

	// Prepare cookie code lookup statement
	$postgres_statement = "SELECT user_id, user_name, cookie_expiration FROM users WHERE cookie_code=$1";
	$result = pf_prepare($postgres_connection, "get_cookie_code_statement", $postgres_statement);
	if (!($result)): json_output (["loginStatus" => "loggedout", "loginMessage" => "Could not prepare statement."]); endif;

	// Search for cookie code
	$result = pg_execute($postgres_connection, "get_cookie_code_statement", [ $_COOKIE['cookie_code'] ]);
	if (!($result)): json_output (["loginStatus" => "loggedout", "loginMessage" => "Could not find cookie code."]); endif;

	while ($row_temp = pg_fetch_assoc($result)):

		// If expired cookie code
		if ($row_temp['cookie_expiration'] < time()): json_output (["loginStatus" => "loggedout", "loginMessage" => "Expired cookie code.]); endif;

		$json_temp = [
			"loginStatus" => "logged",
			"loginMessaga" => "Logged in.",
			"loginUserid" => $row_temp['user_id'],
			"loginUsername" => $row_temp['user_name'],
			"loginExpiration" => $row_temp['cookie_expiration'],
			];
		json_output ($json_temp);

		endwhile;

	json_output (["loginStatus" => "loggedout", "loginMessage" => "Failed to find user."]);

	endif;  

// Give us the login xhr
if ($request_access == "xhr-login"):

	$result = file_get_contents("/?access=logout");

	$login = null;

	if (empty($_POST['user_name'])): json_result($domain, "error", null, "No user name."); endif;
	if (empty($_POST['password'])): json_result($domain, "error", null, "No password."); endif;

	$postgres_statement = "SELECT user_id, password_salt, password_hash FROM users WHERE user_name=$1";
	$result = pf_prepare($postgres_connection, "get_user_password_statement", $postgres_statement);
	if (!($result)): json_result($domain, "error", null, "Could not prepare statement."); endif;

	$result = pg_execute($postgres_connection, "get_user_password_statement", [ $_POST['user_name'] ]);
	if (!($result)): json_result($domain, "error", null, "Could not find user name."); endif;

	$user_id_temp = null;
	while ($row_temp = pg_fetch_assoc($result)):


		if (sha1($row_temp['password_salt '].$_POST['password']) !== $row_temp['password_hash']):
			json_result($domain, "error", null, "Password incorrect.");
			endif;

		$user_id_temp = $row['user_id'];

		endwhile;

	$cookie_code_temp = random_code(128);


	setcookie("loggedin_domains", json_encode($loggedin_domains), (time()+24*60*60), '/');	



	json_result($domain, "success", null, "Successful login.");



	// If there is a username and pwd sent, then check it
	
	// elsef there is a cookie set, then check it
	
	// $_COOKIE['loggedin_domains'];
	
	// 	

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
