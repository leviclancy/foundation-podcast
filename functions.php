<? // Output the CSS
function css_output($style_array=[]) {
	$css_string = null;
	foreach ($style_array as $selector_temp => $properties_array_temp):
		if (empty($properties_array_temp) || !(is_array($properties_array_temp))): continue; endif; // Skip if empty or invalid
		ksort($properties_array_temp);
		$css_string .= $selector_temp . " {";
		foreach ($properties_array_temp as $property_temp => $value_temp):
			if (empty($property_temp) || empty($value_temp)): continue; endif; // Skip if either are empty
			$css_string .= $property_temp . ":" . $value_temp .";";
			endforeach;
		$css_string .= "} ";
		endforeach;
	return $css_string; }

// Closes the AMP page
function amp_footer() {
	echo "</body></html>";
	exit; }

// Outputs a JSON array directly as JSON
function json_output ($json_array) {
	
	global $domain;
	
	header("Content-type: application/json");
	header("Access-Control-Allow-Credentials: true");
	header("Access-Control-Allow-Origin: https://".$domain);
	header("AMP-Access-Control-Allow-Source-Origin: https://".$domain);
	header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");

	echo json_encode($json_array);
	       
	exit; }

// Outputs a JSON array in a formatted way that communicates error reporting
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

	exit; }

// Generates a random code as needed
function random_code($length=16) {
	$characters = [
		"2", "3", "4", "5", "6", "7",
//		"Q", "W", "E", "R". "T", "Y", "U", "I", "O", "P", 
		"Q", "W", "R". "T", "Y", "P", // remove vowels
//		"A", "S", "D", "F", "G", "H", "J", "K", "L", 
		"S", "D", "F", "G", "H", "J", "K", "L", // remove vowels
//		"Z", "X", "C", "V", "B", "N", "M"
		"Z", "C", "V", "B", "N", "M" // remove 'x' for vulgar use
		];
	if (!(is_int($length))): $length = 16; endif;
	if ($length < 1): $length = 16; endif;
	$key_temp = null;
	while (strlen($key_temp) < $length): $key_temp .= $characters[rand(0,31)]; endwhile;
	return $key_temp; }

// Prepares an INSERT/UPDATE statement based on an array of values
function postgres_update_statement ($table_name, $values_temp) {
	
	$columns_list = $bound_list = $updates_list = $count_temp = null;
	foreach ($values_temp as $column_temp => $value_temp):

		$count_temp++;
	
		$comma_temp = ", ";
		if ($count_temp == 1):
			$primary_key = $column_temp;
			$comma_temp = null;
			endif;

		$columns_list .= $comma_temp.$column_temp;
		$bound_list .= $comma_temp."$".$count_temp;
		$updates_list .= $comma_temp.$column_temp."=excluded.".$column_temp;
	
		endforeach;

	return "INSERT INTO $table_name ($columns_list) VALUES ($bound_list) ON CONFLICT ($primary_key) DO UPDATE SET $updates_list";
	}

// Check if the user is logged in
function login_check() {
	
	global $_COOKIE;
	global $domain;
	
	// Now check the login JSON
	$result = file_get_contents("https://".$domain."/?access=json-login");
	$json_decoded = json_decode($result, true);

	// Set up the result
	$message_temp = $result['message'] ? "Login failure.";
	
	// If it did not work...
	if ($json_decoded['loginStatus'] !== "loggedin"): json_result($domain, "error", null, $message_temp); endif;

	} ?>
