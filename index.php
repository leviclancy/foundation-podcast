<? 

// Podcast has three requirements https://developers.google.com/search/docs/guides/podcast-guidelines
	// Podcast must have a homepage https://developers.google.com/search/reference/podcast/homepage-requirements
	// Podcast must have an RSS feed https://developers.google.com/search/reference/podcast/rss-feed
	// Podcast must have at least one episode https://developers.google.com/search/reference/podcast/rss-feed#episode-level

include_once('config.php');

if (empty($_REQUEST['access'])): $_REQUEST['access'] = null; endif;

// Give us the install routine
if ($_REQUEST['access'] == "install"):

	// create users table

	// create podcasts table

	// if nobody in users table then create a user

	exit; endif;

if ($_REQUEST['access'] == "xhr-install"):

	// if there is a user then give error

	// otherwise you can make the user

	endif;

$login = null;

if (empty($login) && in_array($_REQUEST['access'], ["xhr-login", "xhr-"logout", "xhr-save", "admin"])):
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

// Give us the save xhr
if ($_REQUEST['access'] == "xhr-save"):

	// View podcasts

	exit; endif;

// Give us the admin panel
if ($_REQUEST['access'] == "admin"):

	// View podcasts

	// 

	// 
	// Change password

	exit; endif;

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

<? exit; endif; ?>

<html>
<head>
<link rel='alternate' type='application/rss+xml' title='Podcast' href='podcast.ours.foundatoin/podcast.rss' />
<title><? echo $title; ?></title>
</head>
<body>
<h1><? echo $title; ?></h1>
<p><? echo $description; ?></p>
<p>RSS feed: https://<? echo $link; ?>/?view=rss</p>
	
List of amp-audio for each episode
</body>
</html>
