<? // Make sure the URL is correct
if ($request_access !== "rss"): json_result($domain, "error", "/", "Invalid URL."); endif;

// We need to get the page info
$json_page = file_get_contents("https://".$domain."/?access=json-page");
$json_page = json_decode($json_page, true);

header('Content-Type: application/rss+xml; charset=utf-8');

// This is the template
// https://support.google.com/podcast-publishers/answer/9476656?hl=uk#create_feed
// https://github.com/simplepie/simplepie-ng/wiki/Spec:-iTunes-Podcast-RSS Sample iTunes RSS

function simple_tag($tag_temp, $value_temp) {
	
	// Start the tag
	echo "<".$tag_temp.">";
	
	// Loop again if nested
	if (is_array($value_temp)):
		foreach ($value_temp as $tag_temp_temp => $value_temp_temp):
			simple_tag($tag_temp_temp, $value_temp_temp);
			endforeach;
	
	// Or just echo it if it is a string
	else: echo $value_temp; endif;
	
	// Close the tag
	echo "</".$tag_temp.">\n"; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";

// I think iTunes is more finnicky, so we will develop for iTunes and it will still work with Google Play
$attributes_temp = implode(" ", [
//	'xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"',
	'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"',
	'version="2.0"',
	]);
echo "<rss ". $attributes_temp .">\n";

echo "<channel>\n";

$array_temp = [
	"title" 		=> htmlspecialchars($json_page['information']['title']),
	"description" 		=> htmlspecialchars($json_page['information']['description']),
	"language"		=> htmlspecialchars($json_page['information']['language']),
	"link"			=> "https://".$domain,
	"itunes:author"		=> htmlspecialchars($json_page['information']['author']),
//	"itunes:email"		=> $json_page['information']['email'],
	"itunes:owner"		=> [ "itunes:name"=>htmlspecialchars($json_page['information']['author']), "itunes:email"=>$json_page['information']['email'] ],
//	"itunes:category"	=> $json_page['information']['category'],
//	"itunes:category"	=> "Society & Culture",
	];
foreach ($array_temp as $tag_temp => $value_temp):
	simple_tag($tag_temp, $value_temp);
	endforeach;

//echo '<googleplay:image href="http://www.example.com/podcasts/dafnas-zebras/img/dafna-zebra-pod-logo.jpg"/>';

foreach ($json_page['episodes'] as $episode_info):
	
	if ($episode_info['episode_completion'] !== "complete"): continue; endif;
	if ($episode_info['episode_status'] !== "active"): continue; endif;
	
	echo "<item>\n";

		// Permalink
		echo "<guid isPermaLink=\"false\">". $episode_info['episode_id'] ."</guid>\n";

		// Title, description, pubdate
		$array_temp = [
			"title"		=> htmlspecialchars($episode_info['episode_title']),
			"description"	=> htmlspecialchars($episode_info['episode_description']),
			"pubDate"	=> date("D, d M Y", strtotime($episode_info['episode_pubdate'])) ." 12:00:00 GMT", // Tue, 14 Mar 2017 12:00:00 GMT
			];
		foreach ($array_temp as $tag_temp => $value_temp):
			simple_tag($tag_temp, $value_temp);
			endforeach;

		// URL for audio file
		$url_temp = "http://". $domain ."/?episode-file=". $episode_info['episode_id'];

		// Attributes for enclose
		$attributes_temp = implode(" " , [
			'url="'. $url_temp .'"',
			'type="audio/mpeg3"',
			'length="'.$episode_info['episode_bytes_length'].'"',
			]);
		echo "<enclosure ". $attributes_temp ." />\n";

//		echo '<itunes:duration>30:00</itunes:duration>\n';

		echo "</item>\n";

	endforeach;

echo "</channel>\n";
echo "</rss>";

exit; ?>
