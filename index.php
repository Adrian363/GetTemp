<?php
    $ipAdress=$_SERVER["REMOTE_ADDR"];
    $cnt=3; // Number of day to recover weather data
    $timeTest=time();
    
$testCity=0;
if (isset($_POST["sub"])){
    $cityName=$_POST["city"];
    $cityCc=$_POST["cc"];
    $testCity=1;
}

if(!Empty($cityName)){
    $pageTitle="Weather in ".$cityName;
}
else{
    $pageTitle="GetTemp from everywhere";
}

$apiKey = "c369ce229d26af3717b8ae1e71218de8";

$googleApiUrl = "api.openweathermap.org/data/2.5/weather?q=".$cityName."&units=metric&appid=". $apiKey;



// Initialize, could be a lot depends on if we want to make many researches
$ch = curl_init();

// Include or not the header of the return 
curl_setopt($ch, CURLOPT_HEADER, 0);
// Allow us to process the return and not exploit it now, store the rep after the query
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// Set the URL that we want to post
curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// execution of the query 
$response = curl_exec($ch);

$data = json_decode($response);

// Elements to get the forcast for the next 3-6 hours
$ApiUrlForecast="api.openweathermap.org/data/2.5/forecast?q=".$cityName."&units=metric&cnt=".$cnt."&appid=".$apiKey;

curl_setopt($ch, CURLOPT_URL, $ApiUrlForecast);

$response= curl_exec($ch);
$dataForecast=json_decode($response);

// Closing curl
curl_close($ch);
$currentTime = time();

$timeUtc=$dataForecast->city->timezone;


function timeCalculator($time, $utcTime){
    $nbDay=$time/(24*3600); // day since 1st jan 1970
    $deltaTime=$time-intval($nbDay)*24*3600+$utcTime;
    $h=$deltaTime/3600;
    $min=($h-intval($h))*60;

    echo (intval($h)."h".intval($min));
}
?>
<!doctype html> 
    <html>
        <head>
            <title><?php echo $pageTitle ?></title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="#">GetTemp</a>
                <!-- Used when the page is to small to display all the menu items -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent"> <!-- Display in the button if the page size is too smale-->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active"> 
                    <a class="nav-link" href="index.php">Home</a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="jstest.php">Test JS</a>
                </li>
                <li class="nav-item">   
                     <a class="nav-link" href="https://openweathermap.org" target="_blank">API</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://google.com" target="_blank">About</a>
                </li>

            </ul>
            </div>

            </nav>

    <div class="container pt-2">
    <form method="post">
        <div class="form-group">
            <label for="city">City</label><br>
            <input type="text" name="city" class="form-control" placeholder="Enter a city"><br>
            <small id="helpMail" class="form-text text-muted mt-0 pt-0">Please enter a well known city</small>
        </div>
        <button type="submit" name="sub" value="Valid" class="btn btn-primary btn-lg btn-block">Valid</button>
    </form>
    <!-- If city is define -->
    <?php if($testCity==1){ ?>
    
    <div class="report-container">
        <br>
        <h2><?php echo $data->name; ?> Weather Status</h2><br>
        <div class="row">
        <div class="col-sm">
            <h2>Date</h2>
            <div class="time">
                <div><?php echo date("l g:i a", $currentTime); ?></div>
                <div><?php echo date("jS F, Y",$currentTime); ?></div>
                 <div><?php echo ucwords($data->weather[0]->description); ?></div>
            </div>
        </div>
        <div class="weather-forecast col-sm">
            <h2>Forecast for today</h2>
            <img src="http://openweathermap.org/img/w/<?php echo $data->weather[0]->icon; ?>.png" class="weather-icon" /> <br>
                Maximal Temp: <?php echo $data->main->temp_max; ?>°C<br>
                <span class="min-temperature">Minimal Temp: <?php echo ( $data->main->temp_min);?>°C</span>
        </div>
        <div class="time col-sm">
            <h2>Live:</h2>
            <div>Temperature: <?php echo $data->main->temp  ; ?> °C</div>
            <div>Pressure: <?php echo $data->main->pressure; ?> hPa</div>
            <div>Humidity: <?php echo $data->main->humidity; ?> %</div>
            <div>Wind: <?php echo $data->wind->speed; ?> km/h</div>
        </div>
        </div> <br>

        <!-- For the number of ctn, we diplay forecast for every 3 hours-->
        <div class="row">
        <?php for($i=0; $i<$cnt; $i++){ ?>
            <div class="col-sm">
                <h2>Weather for the next <?php echo ($i*3+3) ?> hours</h2>
                <img src="http://openweathermap.org/img/w/<?php echo $dataForecast->list[$i]->weather[0]->icon ?>.png" >
                <div>Temperature: <?php echo $dataForecast->list[$i]->main->temp; ?> °C</div>
                <div>Pressure: <?php echo $dataForecast->list[$i]->main->pressure; ?> hPa</div>
                <div>Humidity: <?php echo $dataForecast->list[$i]->main->humidity; ?> %</div>
                <div>Wind: <?php echo $dataForecast->list[$i]->wind->speed; ?> km/h</div>
            </div>  
        <?php } ?>
        </div>
        <br>
        <div class="jumbotron pt-3">
        <h2>About <?php echo $data->name ?> : </h2>
        <p class="lead"> Some Informations about the city you are looking for ! </p>
        <hr>
        <div class="row">
            <div class="col-sm">
                <p>Country: <?php echo $dataForecast->city->country ?> </p> 
                <p>Population: <?php echo $dataForecast->city->population?> </p>
            </div>  
            <div class="col-sm">
                <p>Latitude: <?php echo $dataForecast->city->coord->lat ?></p>
                <p>Longitude: <?php echo $dataForecast->city->coord->lon ?> </p>
                <p>Sunrise: <?php $timesunrise=$dataForecast->city->sunrise; timeCalculator($timesunrise, $timeUtc); ?> </p>
                <p>Sunset: <?php $timesunset=$dataForecast->city->sunset; timeCalculator($timesunset, $timeUtc); ?> </p>
            </div>
        </div>
        </div>
    </div>
    <?php } ?>
    </div>  
</body>
</html>