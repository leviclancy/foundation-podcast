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
function login_check($return=false) {
	
	// Regardless of whether $return == TRUE or $return == FALSE, we use 'return' on valid login
	// However sometimes we want a gatekeeper mechanism
	// If $return == TRUE then we will return an error if it happens
	// If $return == FALSE then we output and exit and error if it happens

	global $_COOKIE;
	global $_POST;
	
	global $domain;
	
	global $postgres_connection;
	
	// Set cookie code
	$cookie_code_temp = $_COOKIE['cookie_code'] ?? $_POST['cookie_code'] ?? null;

	// If no cookie code, just ignore it
	if (empty($cookie_code_temp)):
		$json_temp['loginMessage'] = "No cookie code.";
		if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;

	// If invalid cookie code
	if (strlen($cookie_code_temp) < 64):
		$json_temp['loginMessage'] = "Invalid cookie code.";
		if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;

	// This means we are accessing it at a point in the script without a Postgres connection
	if (pg_connection_status($postgres_connection) !== PGSQL_CONNECTION_OK):
		
		// Create the HTTP array
		$http_array = [
			"header"  => "Content-type: application/x-www-form-urlencoded\r\n",
			"method"  => "POST",
        		"content" => http_build_query(["cookie_code"=>$cookie_code_temp]),
			];
		$result_temp = file_get_contents("https://".$domain."/?access=json-login", false, stream_context_create(["http" => $http_array]));

		// If we did not connect
		if ($result_temp === FALSE):
			$json_temp['loginMessage'] = "Could not access domain.";
			if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;
	
		// Now decode whatever we received
		$result_temp = json_decode($result_temp, true);
	
		// If it is gibberish then that is an error
		if (!(isset($result_temp['login_status']))):
			$json_temp['loginMessage'] = "Invalid json-login response.";
			if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;

		// The HTTP response, decoded
		if ($return == true): return $result_temp; else: json_output($result_temp); endif;
		
		endif;
	
	// Prepare cookie code lookup statement
	$postgres_statement = "SELECT * FROM podcast_admin_codes WHERE code_type='cookie' AND code_string=$1";
	$result = pg_prepare($postgres_connection, "get_cookie_code_statement", $postgres_statement);
	if (!($result)):
		$json_temp['loginMessage'] = "Could not prepare statement.";
		if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;

	// Search for cookie code
	$result = pg_execute($postgres_connection, "get_cookie_code_statement", [ $cookie_code_temp ]);
	if (!($result)):
		$json_temp['loginMessage'] = "Failed to find matching code.";
		if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;
	
	while ($row_temp = pg_fetch_assoc($result)):

		// If the cookie codes do not match, move on
		if ($cookie_code_temp !== $row_temp['code_string']):
			$json_temp['loginMessage'] = "Mismatched cookie code.";
			if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;

		// If the cookie code is expired, move on
		if ($row_temp['code_expiration'] < time()):
			setcookie("cookie_code", null, 1); // Unset expired cookie
			$json_temp['loginMessage'] = "Expired cookie code.";
			if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;

		// If the cookie code is deactivated, move on
		if ($row_temp['code_status'] == "deactivated"):
			$json_temp['loginMessage'] = "Deactivated cookie code.";
			if ($return == true): return $json_temp; else: json_output($json_temp); endif; endif;

		$json_temp['loginStatus']	= 'loggedin';
		$json_temp['loginMessage']	= 'Logged in.';
		$json_temp['loginAdminID']	= $row_temp['code_admin'];
		$json_temp['loginExpiration']	= $row_temp['code_expiration'];

		return $json_temp;

		endwhile;
	
	$json_temp['loginMessage'] = "Failed to find active code.";
	if ($return == true): return $json_temp; else: json_output($json_temp); endif; } ?>
