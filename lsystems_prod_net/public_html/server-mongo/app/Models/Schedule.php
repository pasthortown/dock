<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Schedule extends Model
{
    protected $collection = 'schedules';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','start_time','end_time',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function schedule_type()
    {
       return $this->embedsOne('App\ScheduleType');
    }

}