<? function amp_header($title_temp) {
	echo "<!doctype html><html amp lang='en'>";
	
	echo "<head><meta charset='utf-8'>";

	if (!(empty($google_analytics_code))):
		echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
		endif;
	
	echo "<script async src='https://cdn.ampproject.org/v0.js'></script>";

	global $domain;
	echo "<link rel='canonical' href='https://". $domain ."'>";

	echo "<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>";

	echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';
	echo '<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>';
	echo '<script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>';
	echo '<script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>';
	echo '<script async custom-element="amp-fx-collection" src="https://cdn.ampproject.org/v0/amp-fx-collection-0.1.js"></script>';
	echo '<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>';

	// Material icons
	echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';

	// Custom fonts
	echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">';
	echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Marck+Script">';
	echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Amiri">';

	echo "<title>". $title_temp ."</title>";

	echo "<meta name='theme-color' content='#2878b4'>";

	echo "<meta name='viewport' content='width=device-width,minimum-scale=1,initial-scale=1'>";

	echo "<style amp-custom>";
	include_once('style.css');
	echo "</style>";

	echo "</head><body>";
	}
						   
function amp_footer() {
	echo "</body></html>";
	exit;
	}

function json_result($domain, $result, $redirect, $message) {
	
	header("Content-type: application/json");
	header("Access-Control-Allow-Credentials: true");
	header("Access-Control-Allow-Origin: https://".$domain);
	header("AMP-Access-Control-Allow-Source-Origin: https://".$domain);

	// No redirect if it was a failure
	if (empty($redirect) || ($result !== "success")):
		header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
		endif;

	// Immediately handle any error message, with no redirect
	if ($result !== "success"):
		header("HTTP/1.0 412 Precondition Failed", true, 412);
		echo json_encode(["result"=>"error", "message"=>$message]);
		exit;
		endif;
	
	if (!(empty($redirect))):	
		header("AMP-Redirect-To: https://".$domain.$redirect);
		header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");
		endif;

	echo json_encode(["result"=>"success", "message"=>$message]);

	exit; } ?>
