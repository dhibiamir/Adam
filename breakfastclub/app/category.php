<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name','description'];

    
    public function items(){

        return $this->hasMany('App\item','idcategory');
    }

}
