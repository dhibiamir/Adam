<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class event extends Model
{
    use SoftDeletes;

    protected $table = 'events';

    public function User(){

        return $this->belongsTo('App\Models\Access\User\User');
    }

    public function history(){
        return $this->hasMany('App\history','idevent');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'location', 'starting_date', 'ending_date', 'assigned_to', 'description'];

    /**
     * @var array
     */
    protected $dates = ['updated_at', 'deleted_at'];


    
    public function items(){

        return $this->hasMany('App\item');
    }


    
    

}


