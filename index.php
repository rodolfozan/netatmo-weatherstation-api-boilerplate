<?php 
	header('Content-Type: text/html; charset=utf8');

/* --------------------------------------------------------------------------------------------------- */	
  // alle Fehler anzeigen
  error_reporting(E_ALL);
  // Fehler in der Webseite anzeigen (nicht in Produktion verwenden)
  ini_set('display_errors', 'On');
  // Fehler in Log-Datei schreiben (absolut oder relativ)
  // ini_set('error_log', '/var/www/virtual/meine-domain.de/logs/php-errors.log');
  ini_set('log_errors', 'On');
  ini_set('error_log', 'php-errors.log');
  // Fehler erzeugen, sodass Log-Datei entsteht
/* --------------------------------------------------------------------------------------------------- */	
	
// Includes language files and Constants
	include("inc/constants.php");
	require_once("classes/language.php");

// Set language
	$language = new language(LANGUAGE);
    $lang = $language->translate();

// Netatmo API Start

// Login Data Netatmo  
	$username		= USERNAME;
	$password		= PASSWORD;
	$app_id			= APPID;
	$app_secret     = APPSECRET;

// Get the Token
	$postdata = array(
	    'grant_type' 	=> "password",
	    'client_id' 	=> $app_id,
	    'client_secret' => $app_secret,
	    'username' 		=> $username,
	    'password' 		=> $password,
	    'scope' 		=> 'read_station'
	);
	
	$url = "https://api.netatmo.net/oauth2/token";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$response = curl_exec($ch);
	curl_close($ch);
    
// Query with Token
	$params     = null;
	$params     = json_decode($response, true);
	$api_url_devices    = "https://api.netatmo.net/api/devicelist?access_token=" . $params['access_token'];
	
	
// Retrieve data (devicelist) and store in array
	$ch_devices = curl_init();
	curl_setopt($ch_devices, CURLOPT_URL, $api_url_devices);
	curl_setopt($ch_devices, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch_devices, CURLOPT_HEADER, 0);
	curl_setopt($ch_devices, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch_devices, CURLOPT_TIMEOUT, 30);
	$array_devices = curl_exec($ch_devices);
	curl_close($ch_devices);
// Netatmo API End

?>

<?php

   //print_r($array_devices);

// Provide measured values
    $netatmo_devices    = json_decode($array_devices,true);

?>

<?php
	
	/* ------------------ OUTDOOR MODUL START ------------------ */
	
	$outdoor_name = $netatmo_devices["body"]["modules"][0]["module_name"];
	$outdoor_time = $netatmo_devices["body"]["modules"][0]["dashboard_data"]["time_utc"];
	$outdoor_temperature = $netatmo_devices["body"]["modules"][0]["dashboard_data"]["Temperature"];
	$outdoor_humidity = $netatmo_devices["body"]["modules"][0]["dashboard_data"]["Humidity"];
	$outdoor_temperature_min = $netatmo_devices["body"]["modules"][0]["dashboard_data"]["min_temp"];
	$outdoor_temperature_min_date = $netatmo_devices["body"]["modules"][0]["dashboard_data"]["date_min_temp"];
	$outdoor_temperature_max = $netatmo_devices["body"]["modules"][0]["dashboard_data"]["max_temp"];
	$outdoor_temperature_max_date = $netatmo_devices["body"]["modules"][0]["dashboard_data"]["date_max_temp"];
		
	/* ------------------ OUTDOOR MODUL END ------------------ */
	
	
	
	/* ------------------ INDOOR MODUL START ------------------ */
	
	$indoor_name = $netatmo_devices["body"]["devices"][0]["module_name"];
	$indoor_time = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["time_utc"];
	$indoor_temperature = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["Temperature"];
	$indoor_humidity = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["Humidity"];
	$indoor_temperature_min = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["min_temp"];
	$indoor_temperature_min_date = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["date_min_temp"];
	$indoor_temperature_max = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["max_temp"];
	$indoor_temperature_min_date = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["date_max_temp"];
	$indoor_co2 = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["CO2"];
	$indoor_noise = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["Noise"];
	$innen_pressure = $netatmo_devices["body"]["devices"][0]["dashboard_data"]["Pressure"];
	
	/* ------------------ INNENMODUL END ------------------ */
	


	/* ------------------ WINDMETER START ------------------ */
	
	$wind_name = $netatmo_devices["body"]["modules"][2]["module_name"];
	$wind_time = $netatmo_devices["body"]["modules"][2]["dashboard_data"]["time_utc"];
	$wind_speed = $netatmo_devices["body"]["modules"][2]["dashboard_data"]["WindStrength"];
	$wind_direction = $netatmo_devices["body"]["modules"][2]["dashboard_data"]["WindAngle"];
	$gusts_speed = $netatmo_devices["body"]["modules"][2]["dashboard_data"]["GustStrength"];
	$gusts_direction = $netatmo_devices["body"]["modules"][2]["dashboard_data"]["GustAngle"];
	$wind_maxspeed = $netatmo_devices["body"]["modules"][2]["dashboard_data"]["max_wind_str"];
	$wind_maxdirection = $netatmo_devices["body"]["modules"][2]["dashboard_data"]["max_wind_angle"];
		
	/* ------------------ WINDMETER END ------------------ */
	
	
	
	/* ------------------ RAIN GAUGE START ------------------ */
	
	$rain_name = $netatmo_devices["body"]["modules"][1]["module_name"];
	$rain_time = $netatmo_devices["body"]["modules"][1]["dashboard_data"]["time_utc"];
	$rain_now = $netatmo_devices["body"]["modules"][1]["dashboard_data"]["Rain"];
	$rain_last_hr = $netatmo_devices["body"]["modules"][1]["dashboard_data"]["sum_rain_1"];
	$rain_last_24hr = $netatmo_devices["body"]["modules"][1]["dashboard_data"]["sum_rain_24"];
   
	/* ------------------ RAIN GAUGE END ------------------ */
	
	
	// Calculate wind direction
	$windPlainText = '';
	function getWindPlainText($windangle) {  
	  if ($windangle       < 11.25) {
		 $windPlainText = "NORD";
	  } elseif ($windangle < 33.75)   {
		 $windPlainText = "NNO";
	  } elseif ($windangle < 56.25) {
		 $windPlainText = "NO";
	  } elseif ($windangle < 78.75)   {
		 $windPlainText = "ONO";
	  } elseif ($windangle < 101.25){
		 $windPlainText = "OST";
	  } elseif ($windangle < 123.75)  {
		 $windPlainText = "OSO";
	  } elseif ($windangle < 146.25){
		$windPlainText = "SO";
	  } elseif ($windangle < 168.75)  {
		$windPlainText = "SSO";
	  } elseif ($windangle < 191.25){
		$windPlainText = "SÜD";
	  } elseif ($windangle < 213.75)  {
		$windPlainText = "SSW";
	  } elseif ($windangle < 236.25){
		$windPlainText = "SW";
	  } elseif ($windangle < 258.75)  {
		$windPlainText = "WSW";
	  } elseif ($windangle < 281.25){
		$windPlainText = "WEST";
	  } elseif ($windangle < 303.75)  {
		$windPlainText = "W-NW";
	  } elseif ($windangle < 326.25){
		$windPlainText = "NW";
	  } elseif ($windangle <= 348.75)  {
		$windPlainText = "NNW";
	  } elseif ($windangle <= 361)  {
		$windPlainText = "NORD";
	  }
	  return $windPlainText;
	}
	
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <title>Netatmo Wetterstation </title>
  </head>
  <body>
    <div class="jumbotron">
	  <div class="container">
		  <h2 class="display-6">Mein Wetter</h2>
		  <p class="lead">Simples Boilerplate Template für die Netatmo Wetterstation</p>
		  <hr class="my-4">
		  <p></p>
	  </div>
	</div>
	
	<div class="container">
	  <div class="row">
		<div class="col-md-3">
			<div class="alert alert-warning" role="alert">
			  <?php echo $outdoor_name . " <font class='small'>" . strftime("%H:%M:%S", $outdoor_time) . "</font>"?>
			</div>
			<table class="table table-bordered">
			  <tr>
			    <td><span class="fas fa-thermometer-full"></span></td>
				<td><?php echo $lang->Outdoor->Temperature ?></td>
				<td><?php echo $outdoor_temperature ?>° C</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-water"></span></td>
				<td><?php echo $lang->Outdoor->Humidity ?></td>
				<td><?php echo $outdoor_humidity ?> %</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-thermometer-quarter"></span></td>
				<td><?php echo $lang->Outdoor->TempMin ?></td>
				<td><?php echo $outdoor_temperature_min ?>° C</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-thermometer-three-quarters"></span></td>
				<td><?php echo $lang->Outdoor->TempMax ?></td>
				<td><?php echo $outdoor_temperature_max ?>° C</td>
			  </tr>
			</table>
		</div>
		<div class="col-md-3">
			<div class="alert alert-danger" role="alert">
			  <?php echo $indoor_name  . " <font class='small'>" . strftime("%H:%M:%S", $indoor_time) . "</font>"?>
			</div>
			<table class="table table-bordered">
			  <tr>
			    <td><span class="fas fa-thermometer-full"></span></td>
				<td><?php echo $lang->Indoor->Temperature ?></td>
				<td><?php echo $indoor_temperature ?>° C</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-water"></span></td>
				<td><?php echo $lang->Indoor->Humidity ?></td>
				<td><?php echo $indoor_humidity ?> %</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-thermometer-quarter"></span></td>
				<td><?php echo $lang->Indoor->TempMin ?></td>
				<td><?php echo $indoor_temperature_min ?>° C</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-thermometer-three-quarters"></span></td>
				<td><?php echo $lang->Indoor->TempMax ?></td>
				<td><?php echo $indoor_temperature_max ?>° C</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-poo"></span></td>
				<td><?php echo $lang->Indoor->Co2 ?></td>
				<td><?php echo $indoor_co2 ?> ppm</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-bell"></span></td>
				<td><?php echo $lang->Indoor->NoiseLevel ?></td>
				<td><?php echo $indoor_noise ?> db</td>
			  </tr>
			</table>
		</div>
		<div class="col-md-3">
			<div class="alert alert-success" role="alert">
			  <?php echo $wind_name  . " <font class='small'>" . strftime("%H:%M:%S", $wind_time) . "</font>"?>
			</div>
			<table class="table table-bordered">
			  <tr>
			    <td><span class="fas fa-wind"></span></td>
				<td><?php echo $lang->Windmeter->Wind ?></td>
				<td><?php echo $wind_speed ?> km/h</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-compass"></span></td>
				<td><?php echo $lang->Windmeter->WindDir ?></td>
				<td><?php echo getWindPlainText($wind_direction) ?> </td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-wind"></span></td>
				<td><?php echo $lang->Windmeter->Gusts ?></td>
				<td><?php echo $gusts_speed ?> km/h</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-compass"></span></td>
				<td><?php echo $lang->Windmeter->GustsDir ?></td>
				<td><?php echo getWindPlainText($gusts_direction) ?> </td>
			  </tr>
			</table>
		</div>
		<div class="col-md-3">
			<div class="alert alert-primary" role="alert">
			  <?php echo $rain_name  . " <font class='small'>" . strftime("%H:%M:%S", $rain_time) . "</font>"?>
			</div>		
			<table class="table table-bordered">
			  <tr>
			    <td><span class="fas fa-cloud-rain"></span></td>
				<td><?php echo $lang->RainGauge->Rain ?></td>
				<td><?php echo $rain_now ?> mm/h</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-cloud-showers-heavy"></span></td>
				<td><?php echo $lang->RainGauge->RainLastHr ?></td>
				<td><?php echo $rain_last_hr ?> mm</td>
			  </tr>
			  <tr>
			    <td><span class="fas fa-cloud-showers-heavy"></span></td>
				<td><?php echo $lang->RainGauge->RainLast24Hr ?></td>
				<td><?php echo $rain_last_24hr ?> mm</td>
			  </tr>
			  
			</table>
		</div>
	  </div>
	</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>


