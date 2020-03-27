<!DOCTYPE html>
<html>
<head>
<title>Weather</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
    function gettingJSON(){
        document.write("jquery loaded");
        $.getJSON("http://api.openweathermap.org/data/2.5/weather?q=London&APPID=c369ce229d26af3717b8ae1e71218de8",function(json){
            document.write(JSON.stringify(json));
        });
    }
    </script>
</head>
<body>
<button id = "getIt" onclick = "gettingJSON()">Get JSON</button>
</body>
</html>