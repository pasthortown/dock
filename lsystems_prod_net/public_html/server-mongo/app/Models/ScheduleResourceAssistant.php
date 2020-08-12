<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class ScheduleResourceAssistant extends Model
{
    protected $collection = 'schedule_resource_assistants';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function schedule_resource_assigment()
    {
       return $this->embedsOne('App\ScheduleResourceAssigment');
    }

}