<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../main/w_style.css">
</head>
<body>
<div class="container">
    <h1 class="text-center mt-4" style="color: #ffffff;">Weather Information</h1>
    <div class="weather-card">
        <form method="POST">
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="location" name="location" class="form-control" placeholder="Enter location" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Get Weather</button>
        </form>

        <?php
        require 'vendor/autoload.php';

        use GuzzleHttp\Client;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $location = htmlspecialchars($_POST['location']);
            $api_url = "https://api.openweathermap.org/data/2.5/weather?q={$location}&appid=570d6bcba80a484fabbf080f31f3f185&units=metric";

            try {
                $client = new Client();
                $response = $client->request('GET', $api_url);
                $weather_data = json_decode($response->getBody(), true);

                if ($weather_data && isset($weather_data['main'])) {
                    $temp = $weather_data['main']['temp'];
                    $description = $weather_data['weather'][0]['description'];
                    $humidity = $weather_data['main']['humidity'];
                    $wind_speed = $weather_data['wind']['speed'];
                    $pressure = $weather_data['main']['pressure'];

                    echo "<div class='weather-details mt-4'>";
                    echo "<p><i class='fas fa-map-marker-alt'></i> <strong>Location:</strong> $location</p>";
                    echo "<p><i class='fas fa-thermometer-half'></i> <strong>Temperature:</strong> {$temp}°C</p>";
                    echo "<p><i class='fas fa-cloud'></i> <strong>Weather:</strong> {$description}</p>";
                    echo "<p><i class='fas fa-tint'></i> <strong>Humidity:</strong> {$humidity}%</p>";
                    echo "<p><i class='fas fa-wind'></i> <strong>Wind Speed:</strong> {$wind_speed} m/s</p>";
                    echo "<p><i class='fas fa-tachometer-alt'></i> <strong>Pressure:</strong> {$pressure} hPa</p>";
                    echo "</div>";
                } else {
                    echo "<p class='mt-4 text-danger'>Could not retrieve weather data for '{$location}'.</p>";
                }
            } catch (Exception $e) {
                echo "<p class='mt-4 text-danger'>Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>
    </div>
</div>
</body>
</html>
