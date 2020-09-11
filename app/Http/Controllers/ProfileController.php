<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    public function index(User $user)
    {
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

        //$postCount = $user->posts->count();
        $postCount = Cache::remember(
            'count.posts.' . $user->id,
            now()->addSeconds(30), 
            function() use($user){
                return $user->posts->count();
            });
        $followersCount = Cache::remember(
            'count.followers.' . $user->id,
            now()->addSeconds(30), 
            function() use($user){
                return $user->profile->followers->count();
            });

        $followingCount = Cache::remember(
            'count.following.' . $user->id,
            now()->addSeconds(30), 
            function() use($user){
                return $user->following->count();
            });
       
        return view('profiles.index',compact('user','follows','postCount','followersCount','followingCount'));
    }

    public function edit(User $user)
    {
    	$this->authorize('update',$user->profile);
    	return view('profiles.edit',compact('user'));
    }

    public function update(User $user)
    {
    	$this->authorize('update',$user->profile);

    	$data = request()->validate([
    		'title'			=>'required',
    		'description'	=>'required',
    		'url'			=>'url',
    		'image'			=>''
    	]);

    	if(request('image'))
    	{
    		$imagepath = request('image')->store('profile','public');

	    	$image = Image::make(public_path("storage/$imagepath")) -> fit(1000,1000);
	    	$image->save();

	    	$imagearray=['image' => $imagepath];
    	}

    	auth()->user()->profile->update(array_merge(
    		$data,
    		$imagearray ?? []
    	));

    	return redirect("/profile/{$user->id}"); 
    }
}
