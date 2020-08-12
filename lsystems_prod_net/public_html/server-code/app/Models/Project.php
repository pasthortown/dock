<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name','date','structure',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function User()
    {
       return $this->hasOne('App\User');
    }

    function ProjectType()
    {
       return $this->hasOne('App\ProjectType');
    }

    function ProjectAttachment()
    {
       return $this->belongsTo('App\ProjectAttachment');
    }

}