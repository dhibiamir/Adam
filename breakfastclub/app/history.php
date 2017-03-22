<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    //
    protected $table = 'histories';

    protected $fillable = ['iditem', 'idevent', 'status','user_id','action'];


    
    /*public function item()
    {
        return $this->hasOne('App\item');
    }


    public function event()
    {
        return $this->hasOne('App\event');
    }*/
}
