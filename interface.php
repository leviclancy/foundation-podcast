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
		"font-family" 	=> "Alegreya, Times",
		"background"	=> "#fff",
		"font-size"	=> "17px",
		],
	"#lightbox-login" =>
		[
		"color"		=> "#fff",
		"background"	=> "rgba(230,106,53,1)",
		],
	"input" => [
		"" 		=> "",
		],
	"*:focus" =>
		[
		"outline"	=> "none",
		"outline-width"	=> "none",
		],
	
	".hide" =>
		[
		"display"	=> "none",
		],
	
	".button-navigation" =>
		[
		"display"	=> "inline-block",
		"padding"	=> "7px 15px",
		"border-radius"	=> "100px",
		"margin"	=> "20px 0 0 20px",
		"font-family"	=> "Verdana",
		"cursor"	=> "pointer",
		"font-size"	=> "80%",
		"border"	=> "2px solid #777",
		],
	
	"#button-navigation-lightbox-login, #button-log-out" =>
		[
		"background"	=> "#777",
		"color"		=> "#fff",
		],

	"#button-lightbox-edit-description, #button-lightbox-edit-episodes, #button-lightbox-edit-admins" =>
		[
		"border"	=> "2px solid #777",
		"background"	=> "#fff",
		"color"		=> "#666",
		],
	
	];

echo "<style amp-custom>" . css_output($style_array) . "</style>";

echo "</head><body>";

echo "<amp-state id='loginState' src='/?access=json-login'></script></amp-state>";
echo "<amp-state id='pageState' src='/?access=json-page'></script></amp-state>";

$lightbox_close_array = implode(",", [
	"lightbox-login.close",
	"lightbox-edit-description.close",
	"lightbox-edit-episodes.close",
	"lightbox-edit-admins.close",
	]);

$result_temp = file_get_contents("/?access=json-login");
echo $result_temp;

// Log in button
echo "<span role='button' tabindex='0' id='button-lightbox-login' class='".$login_hidden."' [class]=\"loginState.loginStatus == 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-login.open'>Log in</span>";

// Log out button
echo "<span role='button' tabindex='0' id='button-log-out' class='".$logout_hidden."' [class]=\"loginState.loginStatus !== 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:'>Log out</span>";

// Edit description
echo "<span role='button' tabindex='0' id='button-lightbox-edit-description' class='".$logout_hidden."' [class]=\"loginState.loginStatus == 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-edit-description.open'>Edit description</span>";

// Edit episodes
echo "<span role='button' tabindex='0' id='button-lightbox-edit-episodes' class='".$logout_hidden."' [class]=\"loginState.loginStatus == 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-edit-episodes.open'>Edit episodes</span>";

// Edit admins
echo "<span role='button' tabindex='0' id='button-lightbox-edit-admins' class='".$logout_hidden."' [class]=\"loginState.loginStatus == 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-edit-admins.open'>Edit admins</span>";

echo '<h1 [text]="pageState.about.title">'. $title .'</h1>';
echo '<p [text]="\'by \' + pageState.about.author">by '. $author .'</p>';
echo '<p [text]="pageState.about.description">'. $description .'</p>';
echo "<p>RSS feed: https://". $domain ."/?access=rss</p>";

echo "<amp-list id='sidebar-navigation-lightbox-search-list' layout='responsive' width='800' height='800' items='.' max-items='100' binding='refresh' reset-on-refresh='always' [src]=\"'/api/search/?search=' + pageState.searchTerm\">";
	echo "<p class='amp-list-fallback' fallback>No search results.</p>";
	echo "<p class='amp-list-fallback' placeholder>Loading search results...</p>";
//	echo "<p class='amp-list-fallback' overflow>Show more.</p>";

	echo "<template type='amp-mustache'>";

		// Include amp-audio
		echo "<span class='categories-item'>";
		echo "<amp-audio width='auto' height'50' src='https://ia801402.us.archive.org/16/items/EDIS-SRP-0197-06/EDIS-SRP-0197-06.mp3'><div fallback>Your browser doesn’t support HTML5 audio</div></amp-audio>";
		echo "<a href='/{{entry_id}}/' target='_blank'><span class='categories-item-title'>{{header}}</span></a>";
		echo "</span>";
		echo "</template>";
	echo "</amp-list>";

// Lightbox for logging in
echo "<amp-lightbox id='lightbox-login' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<form action-xhr='/?access=xhr-login' target='_top' id='login-form' method='post' on='submit:login-form-submit.hide;submit-error:login-form-submit.show'>";
	
	echo "<label class='login-form-label' for='admin_name' form='login-form'>Enter your admin name.</label>";
	echo "<input class='login-form-input' type='text' id='admin_name' name='admin_name' minlength='6' maxlength='50' placeholder='Admin name' required>";

	echo "<label class='login-form-label' for='password' form='install-form'>Enter your password.</label>";
	echo "<input class='login-form-input' type='password' id='password' name='password' minlength='6' maxlength='50' placeholder='Password' required>";

	echo "<div class='form-warning'>";
		echo "<div submitting>Submitting...</div>";
		echo "<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
		echo "<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>";
		echo "</div>";
		
	echo "<span id='login-form-submit' role='button' tabindex='0' on='tap:login-form.submit'>Log in</span>";

	echo "</amp-lightbox>";


// Lightbox for editing the site description
echo "<amp-lightbox id='lightbox-edit-description' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	// 

	echo "</amp-lightbox>";


// Lightbox for editing episodes
echo "<amp-lightbox id='lightbox-edit-episodes' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

		// 

	echo "</amp-lightbox>";

// Lightbox for user management
echo "<amp-lightbox id='lightbox-edit-admins' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	// 

	echo "</amp-lightbox>";


amp_footer(); ?>
