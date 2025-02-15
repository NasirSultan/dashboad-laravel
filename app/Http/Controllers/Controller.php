<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Controller extends \Illuminate\Routing\Controller
{
    public function integrate()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://v2.jokeapi.dev/joke/Any',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
       curl_close($curl);
        $decodedResponse = json_decode($response, true);
       if ($decodedResponse === null) {
            return response()->json(['error' => 'Failed to decode JSON'], 500);
        }
        return response()->json($decodedResponse);
    }
}
