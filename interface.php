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
	"input" => [
		"font-size" 		=> "15px",
		"font-family" 		=> "Verdana",
		],
	
	".form-warning" => [
		"display"		=> "block",
		"margin"		=> "40px auto 30px",
		"font-style"		=> "italic",
		"text-align"		=> "center",
		],
	
	"*:focus" => [
		"outline"		=> "none",
		"outline-width"		=> "none",
		],
	
	".hide" => [
		"display"		=> "none",
		],
	
	".button-navigation" => [
		"display"		=> "inline-block",
		"padding"		=> "7px 15px",
		"border-radius"		=> "100px",
		"margin"		=> "20px 0 0 20px",
		"font-family"		=> "Verdana",
		"cursor"		=> "pointer",
		"font-size"		=> "80%",
		"border"		=> "2px solid #777",
		],
	
	"#button-navigation-lightbox-login, #button-log-out" => [
		"background"		=> "#777",
		"color"			=> "#fff",
		],

	"#button-lightbox-edit-information, #button-lightbox-edit-episodes, #button-lightbox-manage-admins, #button-lightbox-my-account" => [
		"border"		=> "2px solid #777",
		"background"		=> "#fff",
		"color"			=> "#666",
		],
	
	".lightbox-back" => [
		"font-weight"		=> "700",
		"position"		=> "absolute",
		"top"			=> "20px",
		"box-sizing"		=> "border-box",
		"right"			=> "-25px",
		"margin"		=> "0 -1px 0 0",
		"background"		=> "rgba(255,255,255,0.2)",
		"color"			=> "rgba(255,255,255,1)",
		"font-size"		=> "80%",
		"font-family"		=> "Verdana",			
		"padding"		=> "9px 55px 9px 25px",
		"border-radius"		=> "100px",
		"cursor"		=> "pointer",
		"-webkit-transition"	=> "background .25s linear, right .15s ease", // Safari
		"transition"		=> "background .25s linear, right .15s ease",
		],
	
	".lightbox-back:hover" => [
		"right"			=> "-15px",
		"background"		=> "rgba(255,255,255,0.3)",
		"-webkit-transition"	=> "background .5s linear, right .3s ease", // Safari
		"transition"		=> "background .5s linear, right .3s ease",
		],
	
	"amp-lightbox" => [
		"padding"		=> "100px 20px",
		"box-sizing"		=> "border-box",
//		"position"		=> "relative",
		],
	
	"#lightbox-login" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(160deg, rgba(0,65,140,0.2), rgba(255,255,255,0) 40%), linear-gradient(240deg, rgba(255,255,255,0.3), rgba(255,255,255,0) 70%), linear-gradient(15deg, rgba(230,106,53,1), rgba(35,105,190,1))",
		],
			
	".form-label, .form-input" => [
		"width"			=> "80%",
		"max-width"		=> "600px",
		"padding"		=> "20px",
		"display"		=> "block",
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
		"background"		=> "linear-gradient(45deg,rgba(255,255,255,0.5),rgba(255,255,255,0),40%)",
		"box-shadow"		=> "0 0 35px -8px rgba(20,20,20,0.3)",
		"border"		=> "2px solid rgba(255,255,255,0)",
		"-webkit-transition"	=> "background .25s linear, right .15s ease, box-shadow 0.3s linear, border 0.15s linear", // Safari
		"transition"		=> "background .25s linear, right .15s ease, box-shadow 0.3s linear, obrder 0.15s linear",
		],
	
	"#lightbox-edit-information" => [
		"color"			=> "#fff",
		"background"		=> "linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0) 50%), linear-gradient(0deg, rgba(50,150,150,0.7), rgba(80,110,110,0.4)), rgba(45,115,145,1)",
		],
	];

echo "<style amp-custom>" . css_output($style_array) . "</style>";

echo "</head><body>";

echo "<amp-state id='loginState' src='/?access=json-login'></script></amp-state>";
echo "<amp-state id='pageState' src='/?access=json-page'></script></amp-state>";

$lightbox_close_array = implode(",", [
	"lightbox-login.close",
	"lightbox-edit-information.close",
	"lightbox-edit-episodes.close",
	"lightbox-manage-admins.close",
	"lightbox-my-account.close",
	]);

// By default, we are logged out
$login_hidden = "button-navigation"; $logout_hidden = "hide";

// But maybe we are logged in?
if (!(empty($_COOKIE['cookie_code']))):

	// Generate header with post data
	$http_temp = [
		"header"  => "Content-type: application/x-www-form-urlencoded\r\n",
		"method"  => 'POST',
		"content" => http_build_query(["cookie_code" => $_COOKIE['cookie_code']])
		];
	
	// Build context
	$context = stream_context_create(["http" => $http_temp]);

	// Get the result
	$result_temp = file_get_contents("https://".$domain."/?access=json-login", false, $context);

	$result_temp = json_decode($result_temp, true);

	// If we are logged in, update default classes
	if ($result_temp['loginStatus'] == "loggedin"): $login_hidden = "hide"; $logout_hidden = "button-navigation"; endif;
	
	endif;

// Log in button
echo "<span role='button' tabindex='0' id='button-lightbox-login' class='".$login_hidden."' [class]=\"loginState.loginStatus == 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-login.open'>Log in</span>";

// Log out button
echo "<span role='button' tabindex='0' id='button-log-out' class='".$logout_hidden."' [class]=\"loginState.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:logout-form.submit'>Log out</span>";

// Edit information
echo "<span role='button' tabindex='0' id='button-lightbox-edit-information' class='".$logout_hidden."' [class]=\"loginState.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-edit-information.open'>Edit information</span>";

// Edit episodes
echo "<span role='button' tabindex='0' id='button-lightbox-edit-episodes' class='".$logout_hidden."' [class]=\"loginState.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-edit-episodes.open'>Edit episodes</span>";

// Manage admins
echo "<span role='button' tabindex='0' id='button-lightbox-manage-admins' class='".$logout_hidden."' [class]=\"loginState.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-manage-admins.open'>Manage admins</span>";

// My account
echo "<span role='button' tabindex='0' id='button-lightbox-my-account' class='".$logout_hidden."' [class]=\"loginState.loginStatus != 'loggedin' ? 'hide' : 'button-navigation'\" on='tap:". $lightbox_close_array .",lightbox-my-account.open'>My account</span>";

// Logout form

echo "<form action-xhr='/?access=xhr-logout' target='_top' id='logout-form' method='post' on='submit-success:loginState.refresh'>";

//	echo "<div class='form-warning'>";
//		echo "<div submitting>Submitting...</div>";
//		echo "<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
//		echo "<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>";
//		echo "</div>";

echo "</form>";

echo '<h1 [text]="pageState.about.title">'. $title .'</h1>';
echo '<p [text]="\'by \' + pageState.about.author">by '. $author .'</p>';
echo '<p [text]="pageState.about.information">'. $description .'</p>';
echo "<p>RSS feed: https://". $domain ."/?access=rss</p>";

echo "<amp-list id='sidebar-navigation-lightbox-search-list' layout='responsive' width='800' height='800' items='.' max-items='100' binding='refresh' reset-on-refresh='always' [src]=\"'/api/search/?search=' + pageState.searchTerm\">";
	echo "<p class='amp-list-fallback' fallback>No search results.</p>";
	echo "<p class='amp-list-fallback' placeholder>Loading search results...</p>";
//	echo "<p class='amp-list-fallback' overflow>Show more.</p>";

	echo "<template type='amp-mustache'>";

		// Include amp-audio
		echo "<span class='categories-item'>";
		echo "<amp-audio width='auto' height='50' src='https://ia801402.us.archive.org/16/items/EDIS-SRP-0197-06/EDIS-SRP-0197-06.mp3'><div fallback>Your browser doesn’t support HTML5 audio</div></amp-audio>";
		echo "<a href='/{{entry_id}}/' target='_blank'><span class='categories-item-title'>{{header}}</span></a>";
		echo "</span>";
		echo "</template>";
	echo "</amp-list>";

// Lightbox for logging in
echo "<amp-lightbox id='lightbox-login' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";

	echo "<form action-xhr='/?access=xhr-login' target='_top' id='login-form' method='post' on='submit:login-form-submit.hide;submit-error:login-form-submit.show;submit-success:login-form-submit.show,login-form.clear,lightbox-login.close,loginState.refresh'>";
	
	echo "<label class='form-label' for='login-form-admin-name'>Enter your admin name.</label>";
	echo "<input class='form-input' type='text' id='admin_name' name='login-form-admin-name' minlength='6' maxlength='50' placeholder='Admin name' required>";

	echo "<label class='form-label' for='login-form-password'>Enter your password.</label>";
	echo "<input class='form-input' type='password' id='password' name='login-form-password' minlength='6' maxlength='50' placeholder='Password' required>";

	echo "<div class='form-warning'>";
		echo "<div submitting>Submitting...</div>";
		echo "<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
		echo "<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>";
		echo "</div>";
		
	echo "<span class='form-submit' id='login-form-submit' role='button' tabindex='0' on='tap:login-form.submit'>Log in</span>";

	echo "</form>";

	echo "</amp-lightbox>";


// Lightbox for editing the site information
echo "<amp-lightbox id='lightbox-edit-information' on='lightboxOpen:".$lightbox_close_array.";lightboxClose:loginState.refresh' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";

	echo "<form action-xhr='/?access=xhr-' target='_top' id='login-form' method='post' on='submit:edit-decription-form-submit.hide;submit-error:edit-decription-form-submit.show;submit-success:edit-decription-form-submit.show,edit-decription-form.clear'>";
	
	$attributes_temp = [
		"id='edit-decription-form-list'",
		"layout='responsive'",
		"width='800'",
		"height='800'",
		"max-items='1'",
		"reset-on-refresh='always'",
		"items='information'",
		"binding='refresh'",
		"src='amp-state:pageState'",
		"single-item",
		];
	echo "<amp-list ". implode(" ", $attributes_temp) .">
		<span class='amp-list-fallback' fallback>Failed to load information.</span>
		<span class='amp-list-fallback' placeholder>Loading information...</span>
		<span class='amp-list-fallback' overflow>Show more.</span>

		<template type='amp-mustache'>
		
		<label class='form-label' for='title'>Enter the title.</label>
		<input class='form-input' type='text' name='title' minlength='3' maxlength='100' placeholder='Title' value='{{title}}' required>

		<label class='form-label' for='author'>Enter the author.</label>
		<input class='form-input' type='text' name='author' minlength='3' maxlength='100' placeholder='Author' value='{{author}}' required>

		<label class='form-label' for='description'>Enter the description.</label>
		<<textarea class='form-textarea' name='description' minlength='3' maxlength='450' placeholder='Description' required>{{description}}</textarea>

		<label class='form-label' for='title'>Enter the language.</label>
		<input class='form-input' type='text' name='language' minlength='3' maxlength='10' placeholder='Language' value='{{language}}' required>

		</template></amp-list>";

	echo "<div class='form-warning'>";
		echo "<div submitting>Submitting...</div>";
		echo "<div submit-error><template type='amp-mustache'>Error. {{{message}}}</template></div>";
		echo "<div submit-success><template type='amp-mustache'>{{{message}}}</template></div>";
		echo "</div>";

	echo "</form>";

	echo "<span class='form-submit' id='edit-information-form-submit' role='button' tabindex='0' on='tap:edit-decription-form.submit'>Save edits</span>";

	echo "</amp-lightbox>";


// Lightbox for editing episodes
echo "<amp-lightbox id='lightbox-edit-episodes' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";

	// 

	echo "</amp-lightbox>";

// Lightbox for admin management
echo "<amp-lightbox id='lightbox-manage-admins' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";

	// 

	echo "</amp-lightbox>";

// Lightbox for the user's own account
echo "<amp-lightbox id='lightbox-manage-admins' on='lightboxOpen:".$lightbox_close_array."' layout='nodisplay' scrollable>";

	echo "<div class='lightbox-back' on='tap:".$lightbox_close_array."' role='button' tabindex='0'>Back</div>";

	// Password?

	// Admin name

	echo "</amp-lightbox>";

amp_footer(); ?>
