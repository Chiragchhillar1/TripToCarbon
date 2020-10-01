<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\CarbonFootprint;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Http\Resources\TripToCarbon;

class TripController extends Controller
{
	/**
     * check for validations for Trip to carbon API
     * @param  [type] $data [description]
     * @return [type] Validation Response
     */
    private function paramValidator($data)
    {
        $validator = \Validator::make($data, [
            'activity' 		=> 'required|string',
            'activityType' 	=> 'required|in:miles,fuel',
            'country' 		=> 'required|in:usa,gbr',
            'mode' 			=> 'string|required_if:activityType,miles',
            'fuelType'		=> 'string|required_if:activityType,fuel',
            'appTkn'		=> 'string'

        ]);

        return $validator;
    }

    /**
     * Adding available params to URL.
     * Saving request params to DB.
     * 
     * Return - API url.
     */ 
    private function buildUrl($params)
    {

    	$urlParams = '';

    	$saveToDb = [];

        foreach ($params as $key => $value) {

        	$urlParams = $urlParams.$key.'='.$value.'&';

        	$saveToDb[$key] = $value;

        }

        //Remove the last character using rtrim
		$urlParams = rtrim($urlParams, "&");

		$apiUrl = 'https://api.triptocarbon.xyz/v1/footprint?'.$urlParams;

		$carbonFootprint = CarbonFootprint::create($saveToDb); 

        return $apiUrl;    
    }

	/**
	 * Function for fetching carbon foorprint value from 
	 * TripToCarbon API.
	 * 
	 * Caching the Response in Cache-file for a day.
	 * 
	 */ 
    public function getCarbonFootprint(Request $request)
    {
    	$data = $request->all();

    	$validator = $this->paramValidator($data);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $apiUrl = $this->buildUrl($data);

        $client = new Client();

        try {

    		$response = $client->request('GET', $apiUrl);
        	
        } catch (\Exception $e) {

        	return \Response::json(array(
                    'code'      =>  400,
                    'message'   =>  'Bad Request'
                ), 400);   
        	
        }

    	$carbonFootprintResponse =  json_decode($response->getBody(), true);

    	Cache::put('CarbonFootprint'.Carbon::now()->timestamp, $carbonFootprintResponse, 86400);

    	return new TripToCarbon($carbonFootprintResponse);

    }
    
}
