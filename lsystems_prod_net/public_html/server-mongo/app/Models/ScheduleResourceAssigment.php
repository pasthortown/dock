<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class ScheduleResourceAssigment extends Model
{
    protected $collection = 'schedule_resource_assigments';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','date',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function schedule()
    {
       return $this->embedsOne('App\Schedule');
    }

    function resource()
    {
       return $this->embedsOne('App\Resource');
    }

    function guest()
    {
       return $this->embedsMany('App\Guest');
    }

    function responsable()
    {
       return $this->embedsOne('App\Responsable');
    }

}