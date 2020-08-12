<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Guest extends Model
{
    protected $collection = 'guests';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','name','email','identification',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function guest_type()
    {
       return $this->embedsOne('App\GuestType');
    }

}