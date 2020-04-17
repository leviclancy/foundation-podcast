<? // Podcast has three requirements https://developers.google.com/search/docs/guides/podcast-guidelines
	// Podcast must have a homepage https://developers.google.com/search/reference/podcast/homepage-requirements
	// Podcast must have an RSS feed https://developers.google.com/search/reference/podcast/rss-feed
	// Podcast must have at least one episode https://developers.google.com/search/reference/podcast/rss-feed#episode-level

session_start();
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include_once('configuration.php');
include_once('functions.php');

$request_access = ( empty($_REQUEST['access']) ? null : $_REQUEST['access'] );
$request_episode = ( empty($_REQUEST['episode']) ? null : $_REQUEST['episode'] );

if ($request_access == "install"):

	// Create postgres connection
	$postgres_connection = pg_connect("host=$sql_host port=$sql_port dbname=$sql_database user=$sql_user password=$sql_password options='--client_encoding=UTF8'");
	if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK): json_result($domain, "error", null, "Failed database connection."); endif;

	amp_header("Install");

	// Users table
	$tables_array['users'] = [
		"user_id"		=> "VARCHAR(100)", // Fixed user id
		"username"		=> "VARCHAR(200)", // User-changeable username
		"password_key"		=> "VARCHAR(200)", // Unique configuration key
		"password_hash"		=> "VARCHAR(200)", // Hash of password
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
		echo "<p>". $result_temp . "building table '".$table_name."' in '".$database."' database.</p>";

		endforeach;

	// Pull up users if empty
	$sql_temp = "SELECT * FROM users";
	$result = pg_query($postgres_connection, $sql_temp);
	if (empty($result)): echo "<p>Error accessing users table.<p>"; endif;

	while ($row = pg_fetch_row($result)) {
		echo "Author: $row[0]  E-mail: $row[1]";
		echo "<br />\n";
		}

	footer();

	endif;

if ($request_access == "xhr-install"):

	// if there is a user then give error

	// otherwise you can make the user

	endif;



$request_access_array = [
	"xhr-login", "xhr-logout", "xhr-account", "xhr-add", "xhr-update", "admin", // For administration
	"json", "rss", // Output the sitemap
	];

if (!(empty($request_access)) && in_array($request_access, $request_access_array)):
	$connection_pdo = new PDO(
		"mysql:host=$sql_host;dbname=$sql_database;charset=utf8mb4", 
		$sql_user, 
		$sql_password,
		array(PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)  // Number is in seconds
		);
	if (empty($connection_pdo)): echo "Could not connect to mySQL."; exit; endif;
	endif;

$login = null;

if (empty($login) && in_array($request_access, ["xhr-login", "xhr-logout", "xhr-account", "xhr-add", "xhr-update", "admin"])):
	// json of invalid login
	exit; endif;

// Give us the login xhr
if ($request_access == "xhr-login"):

	// Log in

	exit; endif;

// Give us the logout xhr
if ($request_access == "xhr-logout"):

	// Log out

	exit; endif;

// Check the database

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

// Give us the admin panel
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

	footer(); endif;

// Give us the episode
if (($request_access == "json") && array_key_exists($request_episode, $episode_array)):

	// Return the audio files

	endif;

// Give us the JSON
if ($request_access == "json"):

	// JSON of all the episodes

	endif;

// Give us the RSS
if ($request_access == "rss"):

	// Check database 

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

	exit; endif;

amp_header($title);
echo "<h1>". $title ."</h1>";
echo "<p>". $description . "</p>";
echo "<p>RSS feed: https://". $link ."/?view=rss</p>";
echo "List of amp-audio for each episode";
amp_footer(); ?>
