<?php
namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    private $apiKey;

    public function getWeather(string $place)
    {       
        $this->apiKey = config('services.openweather.api_key');
        if (empty($this->apiKey)) {
            return response()->json(
                ['message' => 'No API key provided for OpenWeatherMap'],
                500 // Internal Server Error
            );
        }
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$place}&appid={$this->apiKey}&units=metric";
        $client = new Client();

        try {
            $weatherResponse = $client->request('GET', $url);   
            $weatherResults = json_decode($weatherResponse->getBody(), true);

            return response()->json([
                'place' => $place,
                'weather' => $weatherResults
            ]);
        } catch (Exception $e) {
            Log::error('Error getting weather data for ' . $place);
            Log::error($e->getMessage());

            $statusCode = $e->getCode();

            // You can add more detailed status code handling here based on $e->getCode()

            return response()->json(
                [
                    'message' => 'Error getting weather data for ' . $place,
                    'error' => $e->getMessage(),
                ],
                $statusCode === 0 ? 500 : $statusCode // If getCode is 0, default to 500
            );
        }
    }
}
