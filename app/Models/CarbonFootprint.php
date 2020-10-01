<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CarbonFootprint extends Eloquent
{
    use HasFactory;

    // mongodb connection used
    protected $connection = 'mongodb';

    // mongodb collection used
    protected $collection = 'trip_to_carbon';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['activity', 'activityType', 'country', 'mode', 'fuelType', 'appTkn'];
}
