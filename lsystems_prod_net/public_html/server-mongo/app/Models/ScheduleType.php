<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class ScheduleType extends Model
{
    protected $collection = 'schedule_type';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','name',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}