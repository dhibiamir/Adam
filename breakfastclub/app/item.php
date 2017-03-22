<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class item extends Model
{
    //
    protected $table = 'items';

    protected $fillable = ['name', 'number','idcategory', 'code', 'purchase_date', 'description', 'state' , 'filepath'];



    public function event(){

        //return $this->belongsTo('App\event');

        return $this->belongsToMany('App\event');
    }

    
    public function history()
    {
        return $this->hasMany('App\history','iditem');
    }


    public function category()
    {
        return $this->hasOne('App\category');
    }

}

