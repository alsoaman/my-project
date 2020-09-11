<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
	protected $guarded = [];
	
	public function profileimage()
	{
		$imagepath=($this->image) ?  $this->image: 'profile/Pg5AQEvaT2LdhD0ep3oT3LoPiOMG4T6ic5yGcmui.png';
		return '/storage/' . $imagepath;
	}
	
	public function followers()
	{
		return $this->belongsToMany(User::class);
	}

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
