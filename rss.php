// Make sure the URL is correct
if (!(in_array($request_access, ["install", "xhr-install"]))): json_result($domain, "error", "/", "Invalid URL."); endif;

// Get JSON

// Give us the RSS

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

exit; ?>
