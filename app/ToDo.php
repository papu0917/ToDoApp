<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToDo extends Model
{
    protected $guarded = array('id');
    protected $dates = ['deadline_date'];
    
    public static $rules = array(
        'title' => 'required',
    );
    
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
