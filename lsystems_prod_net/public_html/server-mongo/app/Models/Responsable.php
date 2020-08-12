<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Responsable extends Model
{
    protected $collection = 'responsables';
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

    function user()
    {
       return $this->embedsOne('App\User');
    }

}