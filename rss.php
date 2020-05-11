<? // Make sure the URL is correct
if ($request_access !== "rss"): json_result($domain, "error", "/", "Invalid URL."); endif;

// We need to get the page info
$json_page = file_get_contents("https://".$domain."/?access=json-page");
$json_page = json_decode($json_page, true);

header('Content-Type: application/rss+xml; charset=utf-8');

// This is the template
// https://support.google.com/podcast-publishers/answer/9476656?hl=uk#create_feed

function simple_tag($tag_temp, $value_temp) {
	echo "<".$tag_temp.">" . $value_temp . "</".$tag_temp.">\n";
	}

echo "<" . "?" . "xml version=\"1.0\" encoding=\"UTF-8\"" . "?" . ">\n";

echo "<rss version=\"2.0\" xmlns:googleplay=\"http://www.google.com/schemas/play-podcasts/1.0]\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\">\n";

echo "<channel>\n";

$array_temp = [
	"title" 		=> $json_page['information']['title'],
	"description" 		=> $json_page['information']['description'],
	"language"		=> $json_page['information']['language'],
	"link"			=> $domain,
	"googleplay:author"	=> $json_page['information']['author'],
	"itunes:author"		=> $json_page['information']['author'],
	"googleplay:email"	=> $json_page['information']['email'],
	"itunes:email"		=> $json_page['information']['email'],
	];
foreach ($array_temp as $key_temp => $value_temp):
	simple_tag($tag_temp, $value_temp);
	endforeach;

//echo '<googleplay:image href="http://www.example.com/podcasts/dafnas-zebras/img/dafna-zebra-pod-logo.jpg"/>';

foreach ($json_page['episodes'] as $episode_info):
	
	if ($episode_info['episode_completion'] !== "complete"): continue; endif;
	if ($episode_info['episode_status'] !== "active"): continue; endif;
	
	echo "<item>\n";
		$array_temp = [
			"title"		=> $episode_info['episde_title'],
			"description"	=> $episode_info['episde_description'],
			"pubDate"	=> $episode_info['episde_pubdate'] ." Tue, 14 Mar 2017 12:00:00 GMT",
			];
		foreach ($array_temp as $key_temp => $value_temp):
			simple_tag($tag_temp, $value_temp);
			endforeach;

//		echo '<enclosure url="https://'.$domain.'/?access=podcast-file&episode-id='. episode_info['episde_id'] .'" type="audio/mpeg" length="34216300"/>';
		$attributes_temp = implode(" " , [
			'url="https://'.$domain.'/?access=podcast-file&episode-id='. episode_info['episde_id'] .'"',
			'type="audio/mpeg"',
			'length="34216300"',
			]);
		echo "<enclosure ". $attributes_temp ."/>\n";
//		echo '<itunes:duration>30:00</itunes:duration>\n';
		echo "<guid isPermaLink=\"false\">". $episode_info['episde_id'] ."</guid>\n";
		echo "</item>\n";
	endforeach;

echo "</channel>\n";
echo "</rss>";

exit; ?>
