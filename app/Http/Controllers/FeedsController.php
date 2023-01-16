<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;

class FeedsController extends Controller
{
    public function feeds($start_date,$last_date){
        try {
            //Declaring guzzle object
            $guzzle = new \GuzzleHttp\Client();
            $url = "https://api.nasa.gov/neo/rest/v1/feed";
            $response = $guzzle->request('GET',$url , ['query' => [
                'start_date' => $start_date, 
                'end_date' => $last_date,
                'api_key' => "S2ryrklixpIFQ6no0RSN1jWGpIDjv2gT7hA0TvHF",
            ]]);   

            //Getting Response from Guzzle 
            $content = json_decode($response->getBody());
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $response = json_decode($responseBodyAsString);
            if($response->code == 400){
                return  response()->json([
                    'status' => 'date_error',
                    'message' => 'Maximum interval of days is 7',
                ]);
            }
        }

        //Getting Response from Guzzle 
        $statusCode = $response->getStatusCode();
        $content = json_decode($response->getBody());
        // $diameter_array = [];
        // $speed_array = [];
        // $distance_array = [];
        $chartlabels = [];
        $chartvalues = [];
        $close_data = new stdClass;
        $speed_data = new stdClass;
        $size_data = new stdClass;
        $current_data = new stdClass;
        
        $all_asteroids = $content->near_earth_objects;
        foreach ($all_asteroids as $key => $single_day_asteroids) {
            //Adding llabels and values for chart
            array_push($chartlabels, $key);
            array_push($chartvalues, count($single_day_asteroids));

            //Calculating Merits
            foreach ($single_day_asteroids as $key => $asteroid) {

                //Getting only neccessary items from asteroid data
                $current_data->id = $asteroid->id;
                $current_data->speed = round($asteroid->close_approach_data[0]->relative_velocity->kilometers_per_hour,2);
                $current_data->distance = round($asteroid->close_approach_data[0]->miss_distance->kilometers,2);
                $current_data->diameter = round(($asteroid->estimated_diameter->kilometers->estimated_diameter_max + $asteroid->estimated_diameter->kilometers->estimated_diameter_min)/2 ,2);

                //For testing
                // array_push($diameter_array,$current_data->diameter);
                // array_push($speed_array,$current_data->speed);
                // array_push($distance_array,$current_data->distance);

                //Calculation of Diameter
                if(empty((array)$size_data)){
                    $size_data->id = $current_data->id;
                    $size_data->diameter = $current_data->diameter;
                }
                else{
                    if($current_data->diameter > $size_data->diameter){
                        $size_data->id = $current_data->id;
                        $size_data->diameter = $current_data->diameter;
                    }
                }

                //Calculation of Speed
                if(empty((array)$speed_data)){
                    $speed_data->id = $current_data->id;
                    $speed_data->speed = $current_data->speed;
                }
                else{
                    if($current_data->speed > $speed_data->speed){
                        $speed_data->id = $current_data->id;
                        $speed_data->speed = $current_data->speed;
                    }
                }

                //Calculation of Close Distance
                if(empty((array)$close_data)){
                    $close_data->id = $current_data->id;
                    $close_data->distance = $current_data->distance;
                }
                else{
                    if($current_data->distance > $close_data->distance){
                        $close_data->id = $current_data->id;
                        $close_data->distance = $current_data->distance;
                    }
                }
            }

        } 
        
        //Returning Succes Response
        return response()->json([
            'status' => 'success',
            'message' => 'Fetched Successfully',
            'size_data' => $size_data,
            'speed_data' => $speed_data,
            'close_data' => $close_data,
            'chart_labels' => $chartlabels,
            'chart_values' => $chartvalues,
        ]);
    }   
}
