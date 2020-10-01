<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\CarbonFootprint;
use Illuminate\Support\Facades\Cache;

class TripController extends Controller
{
	/**
     * check for validations for Trip to carbon API
     * @param  [type] $data [description]
     * @return [type]       [description]
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
     * 
     */ 
    private function buildUrl($params)
    {

    	$urlParams = '';

    	$saveDb = [];

        foreach ($params as $key => $value) {

        	$urlParams = $urlParams.$key.'='.$value.'&';

        	$saveDb[$key] = $value;

        }

        //Remove the last character using rtrim
		$urlParams = rtrim($urlParams, "&");

		$url = 'https://api.triptocarbon.xyz/v1/footprint?'.$urlParams;

		$carbonFootprint = CarbonFootprint::create($saveDb); 

        return $url;    
    }

	//main controller
    public function getCarbonFootprint(Request $request)
    {
    	$data = $request->all();

    	$validator = $this->paramValidator($data);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $url = $this->buildUrl($data);

        $client = new Client();

        try {

    		$response = $client->request('GET', $url);
        	
        } catch (\Exception $e) {

        	return \Response::json(array(
                    'code'      =>  400,
                    'message'   =>  'Bad Request'
                ), 400);   
        	
        }

    	$mainResponse =  json_decode($response->getBody(), true);

    	Cache::put('CarbonFootprint', $mainResponse, 120);

    	return $mainResponse;

    }
}
