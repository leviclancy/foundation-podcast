<? // Make sure the URL is correct
if ($request_access !== "rss"): json_result($domain, "error", "/", "Invalid URL."); endif;

// We need to get the page info
$json_page = file_get_contents("https://".$domain."/?access=json-page");
$json_page = json_decode($json_page, true);

header('Content-Type: application/rss+xml; charset=utf-8');

// This is the template
// https://support.google.com/podcast-publishers/answer/9476656?hl=uk#create_feed

echo "<" . "?" . "xml version=\"1.0\" encoding=\"UTF-8\"" . "?" . ">\n\r";

echo '<rss version="2.0" xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">';

echo '<channel>\n';


echo '<title>'. $json_page['information']['title'] .'</title>\r';
echo '<description>'. $json_page['information']['description'] .'</description>\n';
echo '<language>'. $json_page['information']['language'] .'</language>\n';
echo '<link>'. $domain .'</link>\n';

echo '<googleplay:author>'. $json_page['information']['author'] .'</googleplay:author>\n';
echo '<itunes:author>'. $json_page['information']['author'] .'</itunes:author>\n';

echo '<googleplay:email>'. $json_page['information']['email'] .'</googleplay:email>\n';
echo '<itunes:email>'. $json_page['information']['email'] .'</itunes:email>\n';

//echo '<googleplay:image href="http://www.example.com/podcasts/dafnas-zebras/img/dafna-zebra-pod-logo.jpg"/>';

foreach ($json_page['episodes'] as $episode_info):
	
	if ($episode_info['episode_completion'] !== "complete"): continue; endif;
	if ($episode_info['episode_status'] !== "active"): continue; endif;
	
	echo '<item>\n';
		echo '<title>'. episode_info['episde_title'] .'</title>\n';
		echo '<description>'. episode_info['episde_description'] .'</description>\n';
		echo '<pubDate>'. episode_info['episde_pubdate'] .' Tue, 14 Mar 2017 12:00:00 GMT</pubDate>\n';
//		echo '<enclosure url="https://'.$domain.'/?access=podcast-file&episode-id='. episode_info['episde_id'] .'" type="audio/mpeg" length="34216300"/>';
		$attributes_temp = implode(" " , [
			'url="https://'.$domain.'/?access=podcast-file&episode-id='. episode_info['episde_id'] .'"',
			'type="audio/mpeg"',
			'length="34216300"',
			]);
		echo '<enclosure '. $attributes_temp .'/>\n';
//		echo '<itunes:duration>30:00</itunes:duration>\n';
		echo '<guid isPermaLink="false">'. episode_info['episde_id'] .'</guid>\n';
		echo '</item>\n';
	endforeach;

echo '</channel>\n';
echo '</rss>';

exit; ?>
