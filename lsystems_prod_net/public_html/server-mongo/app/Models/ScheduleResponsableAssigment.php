<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class ScheduleResponsableAssigment extends Model
{
    protected $collection = 'schedule_responsable_assigments';
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

    function responsable()
    {
       return $this->embedsOne('App\Responsable');
    }

    function schedule()
    {
       return $this->embedsOne('App\Schedule');
    }

}