<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AvatarHelper;
use App\Helpers\HeroHelper;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Playlist;
use App\Models\User;
use Carbon\Carbon;

class ActivitiesController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        $playlists = $user->activities()->with(['playlist' => function($q){
            Playlist::generateQuery($q);
        }])->where('activitiable_type', Playlist::class)->get()->toArray();
        $articles = $user->activities()->with(['article' => function($q){
            Article::generateQuery($q);
        }])->where('activitiable_type', Article::class)->get()->toArray();

        $response = [];
        foreach(array_merge($playlists, $articles) as $arr){
            $data = [
                'id' => $arr['id'],
                'created_at' => Carbon::parse($arr['created_at'])->timestamp,
                'updated_at' => Carbon::parse($arr['updated_at'])->timestamp,
            ];

            if(isset($arr['playlist'])) {
                $post = $arr['playlist'];
            } else {
                $post = $arr['article'];
            }

            $post['hero'] = HeroHelper::getUrl($post['hero'] ? $post['hero']['name'] : null);
            $post['created_at'] = Carbon::parse($post['created_at'])->timestamp;
            $post['updated_at'] = Carbon::parse($post['updated_at'])->timestamp;
            $post['user'] = $post['minitutor']['user'];
            $post['user']['avatar'] = AvatarHelper::getUrl($post['user']['avatar']);
            unset($post['minitutor']['user']);

            if(isset($arr['playlist'])) {
                $data['playlist'] = $post;
            } else {
                $data['article'] = $post;
            }

            array_push($response, $data);
        }
        $response = array_values(collect($response)->sortBy('updated_at')->reverse()->toArray());

        return response()->json($response, 200);
    }
}
