<? 

// Podcast has three requirements https://developers.google.com/search/docs/guides/podcast-guidelines
	// Podcast must have a homepage https://developers.google.com/search/reference/podcast/homepage-requirements
	// Podcast must have an RSS feed https://developers.google.com/search/reference/podcast/rss-feed
	// Podcast must have at least one episode https://developers.google.com/search/reference/podcast/rss-feed#episode-level

include_once('config.php');

if (empty($_REQUEST['access'])): $_REQUEST['access'] = null; endif;
if (empty($_REQUEST['episode'])): $_REQUEST['episode'] = null; endif;

// Give us the install routine
if ($_REQUEST['access'] == "install"):

	// create database

	// create users table

	// create podcasts table
	// episode_id
	// episode_title
	// episode_description
	// episode_pubdate
	// episode_url
	// episode_duration

	// if nobody in users table then create a user

	exit; endif;

if ($_REQUEST['access'] == "xhr-install"):

	// if there is a user then give error

	// otherwise you can make the user

	endif;

$login = null;

if (empty($login) && in_array($_REQUEST['access'], ["xhr-login", "xhr-"logout", "xhr-account", "xhr-add", "xhr-update", "admin"])):
	// json of invalid login
	exit; endif;

// Give us the login xhr
if ($_REQUEST['access'] == "xhr-login"):

	// Log in

	exit; endif;

// Give us the logout xhr
if ($_REQUEST['access'] == "xhr-logout"):

	// Log out

	exit; endif;

// Check the database

// Give us the account xhr
if ($_REQUEST['access'] == "xhr-account"):

	// View podcasts

	exit; endif;

// Give us the add xhr
if ($_REQUEST['access'] == "xhr-add"):

	// View podcasts

	exit; endif;

// Give us the update xhr
if ($_REQUEST['access'] == "xhr-update"):

	// View podcasts

	exit; endif;

// Give us the admin panel
if ($_REQUEST['access'] == "admin"):

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
if (array_key_exists($_REQUEST['episode'], $episode_array)):

	// Return the audio files

	endif;

// Give us the JSON
if ($_REQUEST['access'] == "json"):

	// JSON of all the episodes

	endif;

// Give us the RSS
if ($_REQUEST['access'] == "rss"):

	// Check database 

	// 

	?>

	<?xml version="1.0" encoding="UTF-8"?>
	<rss version="2.0" xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
	<channel>
		<title><? echo $title; ?></title>
		<googleplay:author><? echo $author; ?></googleplay:author>
		<description><? echo $description; ?></description>
		<googleplay:image href="http://www.example.com/podcasts/dafnas-zebras/img/dafna-zebra-pod-logo.jpg"/>
		<language><? echo $language ?></language>
		<link><? echo $link; ?></link>
		<item>
			<title>Top 10 myths about caring for a zebra</title>
			<description>Here are the top 10 misunderstandings about the care, feeding, and breeding of these lovable striped animals.</description>
			<pubDate>Tue, 14 Mar 2017 12:00:00 GMT</pubDate>
			<enclosure url="https://www.example.com/podcasts/dafnas-zebras/audio/toptenmyths.mp3" type="audio/mpeg" length="34216300"/>
			<itunes:duration>30:00</itunes:duration>
			<guid isPermaLink="false">dzpodtop10</guid>
   			</item>
		</channel>
		</rss>

<? exit; endif;

function amp_header($title_temp) {
	echo "<html>";
	echo "<head>";
	echo "<link rel='alternate' type='application/rss+xml' title='Podcast' href='podcast.ours.foundatoin/podcast.rss' />";
	echo "<title>". $title_temp ."</title>";
	echo "</head>";
	echo "<body>";
	}
						   
function amp_footer() {
	echo "</body></html>";
	exit;
	}
	
amp_header($title);
echo "<h1>". $title ."</h1>";
echo "<p>". $description . "</p>";
echo "<p>RSS feed: https://". $link ."/?view=rss</p>";
echo "List of amp-audio for each episode";
amp_footer(); ?>
