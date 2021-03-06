<? // Make sure the URL is correct
if ($request_access !== "interface"): json_result($domain, "error", "/", "Invalid URL."); endif;

// Get JSON

echo "<!doctype html><html amp lang='en'>";

echo "<head><meta charset='utf-8'>";

if (!(empty($google_analytics_code))):
	echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
	endif;
	
echo "<script async src='https://cdn.ampproject.org/v0.js'></script>";

echo "<link rel='canonical' href='https://". $domain ."'>";

echo "<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>";

echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';
echo '<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>';
echo '<script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>';
echo '<script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>';
echo '<script async custom-element="amp-fx-collection" src="https://cdn.ampproject.org/v0/amp-fx-collection-0.1.js"></script>';
echo '<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>';
echo '<script async custom-element="amp-audio" src="https://cdn.ampproject.org/v0/amp-audio-0.1.js"></script>';

echo '<link href="https://fonts.googleapis.com/css2?family=Alegreya&display=swap" rel="stylesheet">';

echo "<title>". $title_temp ."</title>";

echo "<meta name='theme-color' content='#2878b4'>";

echo "<meta name='viewport' content='width=device-width,minimum-scale=1,initial-scale=1'>";

$style_array = [
	"body" => [
		"font-family" 		=> "Alegreya, Times",
		"background"		=> "#fff",
		"font-size"		=> "17px",
		],
	
	"input, textarea" => [
		"font-size" 		=> "15px",
		"font-family" 		=> "Verdana",
		],
		
	"*:focus" => [
		"outline"		=> "none",
		"outline-width"		=> "none",
		],
	
	".hide" => [
		"display"		=> "none",
		],
	
	"#button-navigation-wrapper" => [
//		"position"		=> "absolute",
//		"right"			=> "1px",
//		"top"			=> "1px",
		"text-align"		=> "right",
		"display"		=> "block",
		"clear"			=> "both",
		"z-index"		=> "100",
		],
	
	".button-navigation" => [
		"display"		=> "inline-block",
		"padding"		=> "7px 15px",
		"border-radius"		=> "100px",
		"margin"		=> "20px 20px 0 0",
		"font-family"		=> "Verdana",
		"cursor"		=> "pointer",
		"font-size"		=> "80%",
		"text-align"		=> "center",
		"border"		=> "2px solid #777",
		],
	
	"#button-navigation-lightbox-login, #button-log-out" => [
		"background"		=> "#777",
		"color"			=> "#fff",
		],

	"#button-lightbox-edit-information, #button-lightbox-edit-episode, #button-lightbox-manage-admins, #button-lightbox-my-account" => [
		"border"		=> "2px solid #777",
		"background"		=> "#fff",
		"color"			=> "#666",
		],
	
	".lightbox-back" => [
		"font-weight"		=> "700",
		"position"		=> "fixed",
		"z-index"		=> "1000",
		"top"			=> "20px",
		"box-sizing"		=> "border-box",
		"left"			=> "-25px",
		"margin"		=> "0 0 0 -1px",
		"background"		=> "rgba(255,255,255,0.2)",
		"color"			=> "rgba(255,255,255,1)",
		"font-size"		=> "80%",
		"font-family"		=> "Verdana",			
		"padding"		=> "9px 25px 9px 47px",
		"border-radius"		=> "100px",
		"cursor"		=> "pointer",
		"-webkit-transition"	=> "background .25s linear, right .15s ease", // Safari
		"transition"		=> "background .25s linear, right .15s ease",
		],
	
	".lightbox-back:hover" => [
		"left"			=> "-15px",
		"background"		=> "rgba(255,255,255,0.3)",
		"-webkit-transition"	=> "background .5s linear, left .3s ease", // Safari
		"transition"		=> "background .5s linear, left .3s ease",
		],
	
	"amp-lightbox" => [
		"padding"		=> "100px 20px",
		"box-sizing"		=> "border-box",
//		"position"		=> "relative",
		"text-align"		=> "center",
		],
	
	"#lightbox-login" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(160deg, rgba(0,65,140,0.2), rgba(255,255,255,0) 40%), linear-gradient(240deg, rgba(255,255,255,0.3), rgba(255,255,255,0) 70%), linear-gradient(15deg, rgba(230,106,53,1), rgba(35,105,190,1))",
		],
			
	".form-wrapper" => [
		"width"			=> "80%",
		"max-width"		=> "600px",
		"padding"		=> "0",
		"margin"		=> "0 auto 0",
		"display"		=> "block",
		"text-align"		=> "left",
		"left"			=> "-18px",
		"position"		=> "relative",
		],
	
	".form-label, .form-input, .form-textarea, .form-file-input" => [
		"width"			=> "80%",
		"max-width"		=> "600px",
		"padding"		=> "20px",
		"display"		=> "block",
		"text-align"		=> "left",
		],

	".form-label" => [
		"font-family"		=> "Verdana",
		"margin"		=> "30px auto 0",
		"font-size"		=> "80%",
		"font-style"		=> "italic",
		],

	".form-input" => [
		"margin"		=> "10px auto 0",
		"border"		=> "2px solid rgba(255,255,255,1)",
		"border-radius"		=> "100px",
		"background"		=> "linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.6))",
		"color"			=> "#333",
		],
	
	".snackbar" => [
		"width"			=> "auto",
		"max-width"		=> "500px",
		"position"		=> "fixed",
		"left"			=> "0px",
		"bottom"		=> "0px",
		"height"		=> "15px",
		"padding"		=> "10px 20px 10px 15px",
		"background"		=> "linear-gradient(45deg, rgba(255,255,255,0.8), rgba(255,255,255,0.6))",
		"box-shadow"		=> "0 0 25px -10px rgba(30,30,30,0.15)",
		"border-radius"		=> "0 15px 0 0",
		"z-index"		=> "3000",
		"text-align"		=> "left",
		"color"			=> "rgba(20,20,20,1)",
		"text-overflow"		=> "ellipsis",
		"overflow"		=> "hidden",
		"white-space"		=> "nowrap",
		"font-size"		=> "80%",
		"font-family"		=> "Verdana",
		],
	
	".form-file-label" => [
		"cursor"		=> "pointer",
		],
	
	".form-checkbox-label" => [
		"cursor"		=> "pointer",
		"padding"		=> "8px 30px",
		"font-family"		=> "Verdana",
		"margin"		=> "20px 0",
		"display"		=> "inline-block",
		"border-radius"		=> "100px",
		"border"		=> "2px solid #fff",
		"position"		=> "relative",
		"z-index"		=> "10",
		],
	
	".form-checkbox-label:before" => [
		"display"		=> "inline-block",
		"font-size"		=> "95%",
		],
	
	".form-checkbox-label:after" => [
		"position"		=> "absolute",
		"bottom"		=> "10px",
		"right"			=> "-190px",
		"z-index"		=> "100",
		"display"		=> "block",
		"font-size"		=> "80%",
		"width"			=> "200px",
		"text-align"		=> "center",
		"opacity"		=> "0.75",
		],
	
	"input + .form-checkbox-label" => [
		"background"		=> "rgba(255,255,255,0)",
		],

	"input + .form-checkbox-label:before" => [
		"content"		=> "'Inactive'",
		],
	
	"input + .form-checkbox-label:after" => [
		"content"		=> "'Tap to make active.'",
		],
	
	"input:checked + .form-checkbox-label" => [
		"background"		=> "rgba(255,255,255,0.15)",
		],

	"input:checked + .form-checkbox-label:before" => [
		"content"		=> "'Active'",
		],
	
	"input:checked + .form-checkbox-label:after" => [
		"content"		=> "'Tap to make inactive.'",
		],

	".form-file-input" => [
		"margin"		=> "10px auto 0",
//		"border"		=> "2px solid rgba(255,255,255,1)",
//		"border-radius"		=> "100px",
//		"background"		=> "linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.6))",
		"color"			=> "#fff",
		],
	
	".form-textarea" => [
		"height"		=> "250px",
		"margin"		=> "10px auto 0",
		"border"		=> "2px solid rgba(255,255,255,1)",
		"border-radius"		=> "20px",
		"background"		=> "linear-gradient(225deg, rgba(255,255,255,0.75), rgba(255,255,255,0.55))",
		"color"			=> "#111",
		],

	".form-submit" => [
		"font-weight"		=> "700",
		"font-family"		=> "Verdana",
		"display"		=> "inline-block",
		"position"		=> "fixed",
		"box-sizing"		=> "border-box",
		"z-index"		=> "1000",
		"bottom"		=> "20px",
		"right"			=> "-15px",
		"margin"		=> "0 -4px 0 0",
		"border-radius"		=> "100px 0 0 100px",
		"background"		=> "rgba(255,255,255,0.1)",
		"padding"		=> "15px 65px 15px 40px",
		"box-shadow"		=> "0 0 25px -8px rgba(20,20,20,0.4)",
		"color"			=> "#fff",
		"text-align"		=> "center",
		"cursor"		=> "pointer",
		"border"		=> "2px solid rgba(255,255,255,0)",
		"-webkit-transition"	=> "background .5s linear, right .3s ease, box-shadow 0.6s linear, border 0.3s linear", // Safari
		"transition"		=> "background .5s linear, right .3s ease, box-shadow 0.6s linear, obrder 0.3s linear",
		],
	
	".form-submit:hover" => [
		"right"			=> "1px",
		"background"		=> "rgba(255,255,255,0.25)",
		"box-shadow"		=> "0 0 35px -8px rgba(20,20,20,0.3)",
		"border"		=> "2px solid rgba(255,255,255,0)",
		"-webkit-transition"	=> "background .25s linear, right .15s ease, box-shadow 0.3s linear,", // Safari
		"transition"		=> "background .25s linear, right .15s ease, box-shadow 0.3s linear",
		],
	
	"#lightbox-edit-information" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0) 50%), linear-gradient(0deg, rgba(50,150,150,0.7), rgba(80,110,110,0.4)), rgba(45,115,145,1)",
		],
	
	"#lightbox-edit-information .snackbar" => [
		"color"			=> "rgba(50,150,150,0.9)",
		],

	"#lightbox-edit-episode" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0) 50%), linear-gradient(0deg, rgba(50,168,105,0.7), rgba(0,110,50,0.4)), rgba(0,156,10,1)",
		],
	
	"#lightbox-edit-episode .snackbar" => [
		"color"			=> "rgba(50,168,105,0.9)",
		],
	
	"#lightbox-delete-episode" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(225deg, rgba(255,255,255,0.3), rgba(255,255,255,0) 50%), linear-gradient(0deg, rgba(230, 86, 34,0.3), rgba(140, 0, 0,0.9)), rgba(255, 55, 33,1)",
		],
	
	"#lightbox-delete-episode .snackbar" => [
		"color"			=> "rgba(230,86,34,0.3)",
		],
	
	"#lightbox-add-episode" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(225deg, rgba(255,255,255,0.3), rgba(255,255,255,0) 50%), linear-gradient(0deg, rgba(171, 89, 179,0.3), rgba(235, 0, 192,0.9)), rgba(153, 41, 93,1)",
		],

	"#lightbox-add-episode .snackbar" => [
		"color"			=> "rgba(171,89,179,0.9)",
		],
	
	"#lightbox-manage-admins" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(225deg, rgba(255,255,255,0.3), rgba(255,255,255,0) 50%), linear-gradient(0deg, rgba(100, 100, 100,0.3), rgba(200, 200, 200,0.8)), rgba(60, 60, 60,1)",
		],
	
	"#lightbox-manage-admins .snackbar" => [
		"color"			=> "rgba(100, 100, 100,0.9)",
		],
	
	"#lightbox-my-account" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(225deg, rgba(255,255,255,0.3), rgba(255,255,255,0) 50%), linear-gradient(0deg, rgba(224, 199, 74, 0.3), rgba(224, 153, 0,0.8)), rgba(255, 208, 0, 1)",
		],

	"#lightbox-my-account .snackbar" => [
		"color"			=> "rgba(224, 199, 74, 0.9)",
		],

	".home-list-item" => [
		"width"			=> "80%",
		"max-width"		=> "600px",
		"margin"		=> "110px auto 50px",
		"padding"		=> "20px 0",
		"display"		=> "block",
		"clear"			=> "both",
		],
	
	".home-list-item-title" => [
		"font-size"		=> "150%",
		"font-weight"		=> "700",
		"display"		=> "block",
		],
	
	".home-list-item-author" => [
		"font-size"		=> "100%",		
		"display"		=> "block",
		],
	
	".home-list-item-description" => [
		"display"		=> "block",
		"white-space"		=> "pre-line",
		"margin"		=> "50px 0 10px",
		],
	
	".home-rss-link" => [
		"display"		=> "block",
		"text-align"		=> "left",
		"margin"		=> "30px auto 30px",
		"padding"		=> "20px 0",
		"font-size"		=> "90%",
		],
	
	".home-rss-link, .home-rss-link a" => [
		"color"			=> "#333",
		],
	
	".episodes-list-item" => [
		"width"			=> "80%",
		"max-width"		=> "600px",
		"margin"		=> "20px auto",
		"padding"		=> "20px 0",
		"display"		=> "block",
		"border-bottom"		=> "3px solid rgba(100,100,100,0.4)",
		"clear"			=> "both",
		],

	".episodes-list-item-title" => [
		"font-size"		=> "110%",
		"font-weight"		=> "700",
		"display"		=> "block",
		],
	
	".episodes-list-item-description" => [
		"font-style"		=> "italic",
		"display"		=> "block",
		],
	
	".episodes-list-item-audio" => [
		"display"		=> "block",
		"margin"		=> "20px 0",
		"width"			=> "90%",
		],

	".episodes-list-item-notes, .episodes-list-item-button" => [
		"font-family"		=> "Verdana",
		"display"		=> "inline-block",
		"font-size"		=> "70%",
		"border-radius"		=> "100px",
		"box-sizing"		=> "border-box",
		"margin"		=> "0 10px 10px 0",
		"padding"		=> "8px 20px",
		"text-transform"	=> "capitalize",
		],
	
	".episodes-list-item-notes" => [
		"background"		=> "#777",
		"border"		=> "2px solid #777",
		"color"			=> "#fff",
		],

	".episodes-list-item-button" => [
		"background"		=> "#fff",
		"border"		=> "2px solid #555",
		"color"			=> "#333",
		"cursor"		=> "pointer",
		],
	
	"#delete-episode-form-alert" => [
		"text-align"		=> "center",
		"font-size"		=> "140%",
		"padding"		=> "60px 30px"
		],
	];

$style_width_array = [ 
	".lightbox-back, .lightbox-back:hover" => [
		"position"		=> "absolute",
		"left"			=> "20px",
		"top"			=> "20px",
		"margin"		=> "0",
		"padding"		=> "9px 25px",
		"border-radius"		=> "100px",
		"display"		=> "inline-block",
		],
	".form-submit, .form-submit:hover" => [
		"position"		=> "relative",
		"right"			=> "1px",
		"top"			=> "1px",
		"margin"		=> "50px auto",
		"padding"		=> "15px 40px",
		"border-radius"		=> "100px",
		"display"		=> "inline-block",
		],
	];

echo "<style amp-custom>";
echo css_output($style_array);
echo "@media only screen and (max-width: 1100px) { ". css_output($style_width_array) ." }";
echo "</style>";

echo "</head><body>";

echo "<amp-state id='pageState' src='/?access=json-page'></amp-state>";

// We need to initialize the login state, and default values
$cookie_code_temp = $_COOKIE['cookie_code'] ?? null;
$http_array = [
	"header"  => "Content-type: application/x-www-form-urlencoded\r\n",
	"method"  => "POST",
        "content" => http_build_query(["cookie_code"=>$cookie_code_temp]),
	];
$json_page = file_get_contents("https://".$domain."/?access=json-page", false, stream_context_create(["http" => $http_array]));
$json_page = json_decode($json_page, true);

$lightbox_close_array = implode(",", [
	"lightbox-login.close",
	"lightbox-edit-information.close",
	"lightbox-edit-episode.close",
	"lightbox-delete-episode.close",
	"lightbox-add-episode.close",
	"lightbox-manage-admins.close",
	"lightbox-my-account.close",
	"episodes-list.changeToLayoutContainer()",
	]);


// By default, we are logged out
$login_hidden = "button-navigation"; $logout_hidden = "hide";
if ($json_page['login']['loginStatus'] == "loggedin"): $login_hidden = "hide"; $logout_hidden = "button-navigation"; endif;

echo "<div id='button-navigation-wrapper'>";

	// Edit information
	echo "<span role='button' tabindex='0' id='button-lightbox-edit-information' class='".$logout_hidden."' [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-edit-information.open'>Edit information</span>";

	// Add episode
	echo "<span role='button' tabindex='0' id='button-lightbox-add-episode' class='".$logout_hidden."' [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-add-episode.open'>Add episode</span>";

	// Manage admins
	echo "<span role='button' tabindex='0' id='button-lightbox-manage-admins' class='".$logout_hidden."' [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-manage-admins.open'>Manage admins</span>";

	// My account
	echo "<span role='button' tabindex='0' id='button-lightbox-my-account' class='".$logout_hidden."' [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-my-account.open'>My account</span>";

	// Log in button
	echo "<span role='button' tabindex='0' id='button-lightbox-login' class='".$login_hidden."' [class]=\"pageState.login.loginStatus == 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-login.open'>Log in</span>";

	// Log out button
	echo "<span role='button' tabindex='0' id='button-log-out' class='".$logout_hidden."' [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:logout-form.submit'>Log out</span>";

	echo "</div>";


// Logout form
echo "<form action-xhr='/?access=xhr-logout' target='_top' id='logout-form' method='post' on='submit-success:logout-form.clear,pageState.refresh'>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
//	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

echo "<div class='home-list-item'>";

	echo "<h1 class='home-list-item-title' [text]='pageState.information.title'>". $json_page['information']['title'] ."</h1>";

	$author_temp = $json_page['information']['author'];
	if (!(empty($author_temp))): $author_temp = "by " . $author_temp; endif;
	echo "<div class='home-list-item-author' [text]=\"pageState.information.author == '' ? '' : 'by ' + pageState.information.author\">". $author_temp ."</div>";

	echo "<div class='home-list-item-description' [text]='pageState.information.description'>". $json_page['information']['description'] ."</div>";

	echo "<p class='home-rss-link'>RSS: <a href='https://". $domain ."/?access=rss'>". $domain ."/?access=rss</a></p>";

	echo "</div>";
		
// Handle if more than 50 episodes
$attributes_temp = implode(" ", [
	"id='episodes-list'",
	"layout='responsive'",
	"width='600'",
	"height='300'",
	"items='episodes'",
//	"binding='refresh'",
//	"src='amp-state:pageState'",
	"src='/?access=json-page'",
//	"max-items='50'",
	"load-more-bookmark='next'",
	"load-more='manual'",
	]);
echo "<amp-list ". $attributes_temp .">";
	echo "<span fallback>Failed to load episodes.</span>
	<span placeholder>Loading episodes...</span>
	<span overflow>Show more.</span>";

	echo "<template type='amp-mustache'>";
	
		echo "<div class='episodes-list-item'>";
	
		// Podcast title and description
		echo "{{#episode_title}}<div class='episodes-list-item-title'>{{episode_title}}</div>{{/episode_title}}";
		echo "{{#episode_description}}<div class='episodes-list-item-description'>{{episode_description}}</div>{{/episode_description}}";
		
		// Podcast audio
		echo "<amp-audio class='episodes-list-item-audio' width='auto' height='50' src='/?episode-file={{episode_id}}'><div fallback>Your browser doesn’t support HTML5 audio</div></amp-audio>";

//		// Just show the episode id
//		echo "<span [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'episodes-list-item-notes'\" class='".$logout_hidden."'>{{episode_id}}</span>";

		// Just show the date
		echo "<span class='episodes-list-item-notes'>{{episode_pubdate_fancy}}</span>";

		// Just shows 'active' or 'inactive' status
		echo "<span [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'episodes-list-item-notes'\" class='".$logout_hidden."'>{{episode_status}}</span>";

		// Just shows 'complete' or 'incomplete' status
		echo "<span [class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'episodes-list-item-notes'\" class='".$logout_hidden."'>{{episode_completion}}</span>";

		// We have to remove " from around keys and values
		$set_state_array_temp = str_replace('"', null, "AMP.setState(".json_encode([
			"editEpisode" => [
				"editEpisodeID" => "'{{episode_id}}'",
				"editEpisodeTitle" => "'{{episode_title}}'",
				"editEpisodeDescription" => "'{{episode_description}}'",
				"editEpisodePubDate" => "'{{episode_pubdate}}'",
//				"editEpisodeDuration" => "'{{episode_duration}}'",
				"editEpisodeStatus" => "'{{episode_status}}'",
				], ]).")");

		// Set up edit button
		$attributes_temp = implode(" ", [
			"role='button'",
			"tabindex='0'",
			"class='".$logout_hidden."'",
			"[class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'episodes-list-item-button'\"",
			"on=\"tap:". implode(",", [$set_state_array_temp, $lightbox_close_array, "lightbox-edit-episode.open"])."\"",
//			"on=\"tap:AMP.setState({editEpisodeID: '{{episode_id}}'}),".$lightbox_close_array.",lightbox-edit-episode.open\"",
			]);

		// Edit episode button
		echo "<span ". $attributes_temp .">Edit</span>";

		// We have to remove " from around keys and values
		$set_state_array_temp = str_replace('"', null, "AMP.setState(".json_encode([
			"deleteEpisode" => [
				"deleteEpisodeID" => "'{{episode_id}}'",
				], ]).")");

		// Set up delete button
		$attributes_temp = implode(" ", [
			"role='button'",
			"tabindex='0'",
			"class='".$logout_hidden."'",
			"[class]=\"pageState.login.loginStatus != 'loggedin' ? 'hide' : 'episodes-list-item-button'\"",
			"on=\"tap:". implode(",", [$set_state_array_temp, $lightbox_close_array, "lightbox-delete-episode.open"])."\"",
			]);

		// Delete episode button
		echo "<span ". $attributes_temp .">Delete</span>";

		echo "</template>";
		
	echo "</amp-list>";

// Lightbox for logging in
echo "<amp-lightbox id='lightbox-login' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";

	echo "<form action-xhr='/?access=xhr-login' target='_top' id='login-form' method='post' on='submit:login-form-submit.hide;submit-error:login-form-submit.show;submit-success:login-form-submit.show,login-form.clear,lightbox-login.close,pageState.refresh,episodes-list.refresh'>";
	
	echo "<label class='form-label' for='login-form-admin-name'>Enter your admin name.</label>";
	echo "<input class='form-input' type='text' id='login-form-admin-name' name='login-form-admin-name' minlength='6' maxlength='50' placeholder='Admin name' required>";

	echo "<label class='form-label' for='login-form-password'>Enter your password.</label>";
	echo "<input class='form-input' type='password' id='login-form-password' name='login-form-password' minlength='6' maxlength='50' placeholder='Password' required>";
		
	echo "<span class='form-submit' id='login-form-submit' role='button' tabindex='0' on='tap:login-form.submit'>Log in</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	echo "</amp-lightbox>";


// Lightbox for editing the site information
echo "<amp-lightbox id='lightbox-edit-information' on=\"lightboxOpen:".$lightbox_close_array.",AMP.setState({editInformationBack: 'Back'});lightboxClose:edit-information-form.clear,pageState.refresh\" layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0' [text]='editInformationBack'>Back</div>";

	$attributes_temp = implode(" ", [
		"action-xhr='/?access=xhr-edit-information'",
		"target='_top'",
		"id='edit-information-form'",
		"method='post'",
		'on="submit:edit-information-form-submit.hide;submit-error:edit-information-form-submit.show;submit-success:AMP.setState({editInformationBack: \'Back\'}),edit-information-form-submit.show,pageState.refresh"',
		]);

	echo "<form ". $attributes_temp .">";
		
	echo "<label class='form-label' for='edit-information-title'>Enter the title.</label>";
	echo "<input class='form-input' type='text' id='edit-information-title' name='edit-information[title]' minlength='3' maxlength='100' placeholder='Title' [value]=\"pageState.information.title\" on=\"input-throttled:AMP.setState({editInformationBack: 'Back without saving'})\" value='' required>";

	echo "<label class='form-label' for='edit-information-author'>Enter the author.</label>";
	echo "<input class='form-input' type='text' id='edit-information-author' name='edit-information[author]' minlength='3' maxlength='100' placeholder='Author' [value]=\"pageState.information.author\" on=\"input-throttled:AMP.setState({editInformationBack: 'Back without saving'})\" value='' required>";

	echo "<label class='form-label' for='edit-information-description'>Enter the description.</label>";
	echo "<textarea class='form-textarea' id='edit-information-description' name='edit-information[description]' minlength='3' maxlength='1000' placeholder='Description' [defaultText]=\"pageState.information.description\" on=\"input-throttled:AMP.setState({editInformationBack: 'Back without saving'})\" required></textarea>";

	echo "<label class='form-label' for='edit-information-email'>Enter the email.</label>";
	echo "<input class='form-input' type='email' id='edit-information-email' name='edit-information[email]' minlength='3' maxlength='50' placeholder='Email' [value]=\"pageState.information.email\" on=\"input-throttled:AMP.setState({editInformationBack: 'Back without saving'})\" value='' required>";

	echo "<label class='form-label' for='edit-informationlanguage'>Enter the language.</label>";
	echo "<input class='form-input' type='text' id='edit-information-language' name='edit-information[language]' minlength='3' maxlength='10' placeholder='Language' [value]=\"pageState.information.language\" on=\"input-throttled:AMP.setState({editInformationBack: 'Back without saving'})\" value='' required>";

	echo "<span class='form-submit' id='edit-information-form-submit' role='button' tabindex='0' on='tap:edit-information-form.submit'>Save information</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	echo "</amp-lightbox>";


// Lightbox for editing an episode
echo "<amp-lightbox id='lightbox-edit-episode' on=\"lightboxOpen:".$lightbox_close_array.",AMP.setState({editEpisodeBack: 'Back'});lightboxClose:edit-episode-form.clear,episodes-list.refresh\" layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0' [text]='editEpisodeBack'>Back</div>";

	$attributes_temp = implode(" ", [
		"action-xhr='/?access=xhr-edit-episode'",
		"target='_top'",
		"id='edit-episode-form'",
		"method='post'",
		'on="submit:edit-episode-form-submit.hide;submit-error:edit-episode-form-submit.show;submit-success:AMP.setState({editEpisodeBack: \'Back\'}),edit-episode-form-submit.show,episodes-list.refresh"',
		]);

	echo "<form ".$attributes_temp.">";

	echo "<input type='hidden' name='edit-episode[episode_id]' [value]='editEpisode.editEpisodeID' required>";

	echo "<label class='form-label' for='edit-episode-title'>Enter the episode title.</label>";
	echo "<input class='form-input' type='text' id='edit-episode-title' name='edit-episode[episode_title]' minlength='3' maxlength='100' placeholder='Title' [value]='editEpisode.editEpisodeTitle' on=\"input-throttled:AMP.setState({editEpisodeBack: 'Back without saving'})\" required>";

	echo "<label class='form-label' for='edit-episode-description'>Enter the episode description.</label>";
	echo "<textarea class='form-textarea' id='edit-episode-description' name='edit-episode[episode_description]' minlength='3' maxlength='450' placeholder='Description' [defaultText]='editEpisode.editEpisodeDescription' on=\"input-throttled:AMP.setState({editEpisodeBack: 'Back without saving'})\" required></textarea>";

	echo "<label class='form-label' for='edit-episode-pubdate'>Enter the publication date.</label>";
	echo "<input class='form-input' type='date' id='edit-episode-pubdate' name='edit-episode[episode_pubdate]' minlength='3' maxlength='10' placeholder='today' [value]='editEpisode.editEpisodePubDate' on=\"input-throttled:AMP.setState({editEpisodeBack: 'Back without saving'})\" required>";

//	echo "<label class='form-label' for='edit-episode-duration'>Enter the duration.</label>";
//	echo "<input class='form-input' type='date' id='edit-episode-duration' name='edit-episode[episode_duration]' minlength='3' maxlength='10' placeholder='Duration'  [value]='editEpisode.editEpisodeDuration' on=\"input-throttled:AMP.setState({editEpisodeBack: 'Back without saving'})\" required>";

	echo "<input type='hidden' name='edit-episode[episode_status]' value='inactive'>";

	echo "<label class='form-label' for='edit-episode-status'>Episode active status.</label>";
	echo "<div class='form-wrapper'>";
	echo "<input type='checkbox' id='edit-episode-status' name='edit-episode[episode_status]' value='active' on=\"input-throttled:AMP.setState({editEpisodeBack: 'Back without saving'})\" [checked]=\"editEpisode.editEpisodeStatus != 'active' ? false : true \" hidden>";
	echo "<label class='form-checkbox-label' for='edit-episode-status'></label>";
	echo "</div>";

//	echo "<amp-audio width='auto' src='https://ia801402.us.archive.org/16/items/EDIS-SRP-0197-06/EDIS-SRP-0197-06.mp3'>";
//	echo "<div fallback>Your browser doesn’t support HTML5 audio.</div>";
//	echo "</amp-audio>";

	echo "<span class='form-submit' id='edit-episode-form-submit' role='button' tabindex='0' on='tap:edit-episode-form.submit'>Save episode</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	echo "</amp-lightbox>";

// Lightbox for deleting an episode
echo "<amp-lightbox id='lightbox-delete-episode' on=\"lightboxOpen:".$lightbox_close_array.";lightboxClose:delete-episode-form.clear,delete-episode-form-submit.show,episodes-list.refresh\" layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>No, go back</div>";

	$attributes_temp = implode(" ", [
		"action-xhr='/?access=xhr-delete-episode'",
		"target='_top'",
		"id='delete-episode-form'",
		"method='post'",
		'on="submit:delete-episode-form-submit.hide;submit-error:delete-episode-form-submit.show;submit-success:delete-episode-form-submit.show,lightbox-delete-episode.close,delete-episode-form.clear,episodes-list.refresh"',
		]);

	echo "<form ".$attributes_temp.">";

	echo "<input type='hidden' name='delete-episode[episode_id]' [value]='deleteEpisode.deleteEpisodeID' required>";

	echo "<p id='delete-episode-form-alert'>Are you sure you want to delete this episode?</p>";

	echo "<span class='form-submit' id='delete-episode-form-submit' role='button' tabindex='0' on='tap:delete-episode-form.submit'>Yes, delete episode</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	echo "</amp-lightbox>";

// Lightbox for adding episodes
echo "<amp-lightbox id='lightbox-add-episode' on=\"lightboxOpen:".$lightbox_close_array.",AMP.setState({addEpisodeBack: 'Back', addEpisodeLabel: ''}),add-episode-form.clear;lightboxClose:add-episode-form.clear,".$lightbox_close_array."\" layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0' [text]='addEpisodeBack'>Back</div>";

	$attributes_temp = implode(" ", [
		"action-xhr='/?access=xhr-add-episode'",
		"target='_top'",
		"id='add-episode-form'",
		"method='post'",
		'on="submit:add-episode-form-submit.hide;submit-error:add-episode-form-submit.show;submit-success:AMP.setState({addEpisodeBack: \'Back\'}),lightbox-add-episode.close,add-episode-form-submit.show,episodes-list.refresh"',
		]);

	echo "<form ".$attributes_temp.">";

	echo "<label class='form-label form-file-label' for='add-episode'>Click to add MP3 file.</label>";
	echo "<input type='file' class='form-file-input' id='add-episode' name='add-episode' placeholder='Add MP3 file' accept='.mp3,audio/mpeg3' on=\"input-throttled:AMP.setState({addEpisodeBack: 'Back without adding'})\">";

	echo "<span class='form-submit' id='add-episode-form-submit' role='button' tabindex='0' on='tap:add-episode-form.submit'>Add episode</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	echo "</amp-lightbox>";

// Lightbox for managing admins
echo "<amp-lightbox id='lightbox-manage-admins' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";

	// Want to add a new user?
	// Send them this link, valid for 12 hours
	// It'll let them create a new account

	$attributes_temp = implode(" ", [
		"action-xhr='/?access=xhr-delete-episode'",
		"target='_top'",
		"id='delete-episode-form'",
		"method='post'",
		'on="submit:delete-episode-form-submit.hide;submit-error:delete-episode-form-submit.show;submit-success:delete-episode-form-submit.show,lightbox-delete-episode.close,delete-episode-form.clear,episodes-list.refresh"',
		]);

	echo "<form ".$attributes_temp.">";

	// Handle if more than 50 episodes
	$attributes_temp = implode(" ", [
		"id='admins-list'",
		"layout='responsive'",
		"width='600'",
		"height='300'",
		"items='.'",
//		"binding='refresh'",
//		"src='amp-state:pageState'",
		"src='/?access=json-admins'",
//		"max-items='50'",
//		"load-more-bookmark='next'",
//		"load-more='manual'",
		]);
	echo "<amp-list ". $attributes_temp .">";
		echo "<span fallback>Failed to load admins.</span>
		<span placeholder>Loading admins...</span>
		<span overflow>Show more.</span>";

		echo "<template type='amp-mustache'>";
	
			echo "<div class='admins-list-item'>";

			echo "{{admin_name}}";

			// Inactive/activate

			// Generate magic link button

			echo "</div>";

			echo "<div class='admins-list-item-magic-link'>";

			// Close button

			echo "Please send magic link to user. Link expires in one hour.";

			// View link

			echo "</div>";

			echo "</template>";
		echo "</amp-list>";

	echo "<span class='form-submit' id='add-episode-form-submit' role='button' tabindex='0' on='tap:add-episode-form.submit'>Save changes</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	echo "</amp-lightbox>";

// Lightbox for the user's own account
echo "<amp-lightbox id='lightbox-my-account' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";


	// Form to update admin name
	$attributes_temp = implode(" ", [
		"action-xhr='/?access=xhr-add-episode'",
		"target='_top'",
		"id='add-episode-form'",
		"method='post'",
		'on="submit:add-episode-form-submit.hide;submit-error:add-episode-form-submit.show;submit-success:AMP.setState({addEpisodeBack: \'Back\'}),lightbox-add-episode.close,add-episode-form-submit.show,episodes-list.refresh"',
		]);
	echo "<form ".$attributes_temp.">";

	echo "<label class='form-label' for='edit-episode-pubdate'>Enter the publication date.</label>";
	echo "<input class='form-input' type='date' id='edit-episode-pubdate' name='edit-episode[episode_pubdate]' minlength='3' maxlength='10' placeholder='today' [value]='editEpisode.editEpisodePubDate' on=\"input-throttled:AMP.setState({editEpisodeBack: 'Back without saving'})\" required>";

	echo "<span class='form-submit' id='add-episode-form-submit' role='button' tabindex='0' on='tap:add-episode-form.submit'>Add episode</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	// Form to update admin password
	$attributes_temp = implode(" ", [
		"action-xhr='/?access=xhr-add-episode'",
		"target='_top'",
		"id='add-episode-form'",
		"method='post'",
		'on="submit:add-episode-form-submit.hide;submit-error:add-episode-form-submit.show;submit-success:AMP.setState({addEpisodeBack: \'Back\'}),lightbox-add-episode.close,add-episode-form-submit.show,episodes-list.refresh"',
		]);
	echo "<form ".$attributes_temp.">";

	echo "<label class='form-label' for='edit-episode-pubdate'>Enter the publication date.</label>";
	echo "<input class='form-input' type='date' id='edit-episode-pubdate' name='edit-episode[episode_pubdate]' minlength='3' maxlength='10' placeholder='today' [value]='editEpisode.editEpisodePubDate' on=\"input-throttled:AMP.setState({editEpisodeBack: 'Back without saving'})\" required>";

	echo "<span class='form-submit' id='add-episode-form-submit' role='button' tabindex='0' on='tap:add-episode-form.submit'>Add episode</span>";

	echo "<div class='snackbar' submitting>Submitting...</div>";
	echo "<div class='snackbar' submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
	echo "<div class='snackbar' submit-success><template type='amp-mustache'>{{{message}}}</template></div>";

	echo "</form>";

	echo "</amp-lightbox>";

amp_footer(); ?>
