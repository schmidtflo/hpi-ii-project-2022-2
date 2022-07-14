<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $data = collect(\App\Models\Article::search('rhomberg sersa')->must(new \JeroenG\Explorer\Domain\Syntax\MultiMatch('rhomberg sersa', ['title', 'content'], 0))->take(10000)->raw()->hits());
    dd($data->count());
    $minScore = $data->first()['_score'] / 10;
//    dump($minScore);
    $data = $data->filter(function ($item, $key) use ($minScore) {
        return $item['_score'] > $minScore;
    })->pluck('_id');
//    dd(\App\Models\Article::findMany($data)->last());
    return view('test', ['data' => $data, 'count' => count($data)]);
});

Route::get('search', [\App\Http\Controllers\HomeController::class, 'show']);
