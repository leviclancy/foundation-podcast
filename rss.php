<? // Make sure the URL is correct
if ($request_access !== "rss"): json_result($domain, "error", "/", "Invalid URL."); endif;

// We need to get the page info
$json_page = file_get_contents("https://".$domain."/?access=json-page");
$json_page = json_decode($json_page, true);

header('Content-Type: application/rss+xml; charset=utf-8');

// This is the template
// https://support.google.com/podcast-publishers/answer/9476656?hl=uk#create_feed

echo '<' . '?' . 'xml version="1.0" encoding="UTF-8" ' . '?' . '>';

echo '<rss version="2.0" xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">';

echo '<channel>';

	echo '<title>'. $json_page['information']['title'] .'</title>';
echo '<googleplay:author>'. $json_page['information']['author'] .'</googleplay:author>';
echo '<description>'. $json_page['information']['description'] .'</description>';
//echo '<googleplay:image href="http://www.example.com/podcasts/dafnas-zebras/img/dafna-zebra-pod-logo.jpg"/>';
echo '<itunes:email>'. $json_page['information']['email'] .'</itunes:email>';
echo '<language>'. $json_page['information']['language'] .'</language>';
echo '<link>'. $domain .'</link>';

foreach (json_page['episodes'] as $episode_info):
	
	if ($episode_info['episode_completion'] !== "complete"): continue; endif;
	if ($episode_info['episode_status'] !== "active"): continue; endif;
	
	echo '<item>';
		echo '<title>'. episode_info['episde_title'] .'</title>';
		echo '<description>'. episode_info['episde_description'] .'</description>';
		echo '<pubDate>'. episode_info['episde_pubdate'] .' Tue, 14 Mar 2017 12:00:00 GMT</pubDate>';
//		echo '<enclosure url="https://'.$domain.'/?access=podcast-file&episode-id='. episode_info['episde_id'] .'" type="audio/mpeg" length="34216300"/>';
		echo '<enclosure url="https://'.$domain.'/?access=podcast-file&episode-id='. episode_info['episde_id'] .'" type="audio/mpeg" />';
//		echo '<itunes:duration>30:00</itunes:duration>';
		echo '<guid isPermaLink="false">'. episode_info['episde_id'] .'</guid>';
		echo '</item>';
	endforeach;

echo '</channel></rss>';

exit; ?>
