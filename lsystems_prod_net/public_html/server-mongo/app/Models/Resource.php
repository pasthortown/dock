<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Resource extends Model
{
    protected $collection = 'resources';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','fullname','join_data','capacity',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function resource_type()
    {
       return $this->embedsOne('App\ResourceType');
    }

}