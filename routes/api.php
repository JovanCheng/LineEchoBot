<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//網址傳入: https://example.dev/api/push_text?message=訊息&to=abcdefgj使用者IDijklomn
Route::get('push_text', function(Request $request){

    $message=$request->message;
    $to=$request->to;

    $bot = resolve('LINE\LINEBot');
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
    $bot->pushMessage($to, $textMessageBuilder);

    return response('OK', 200);
});
