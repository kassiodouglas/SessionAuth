<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['SessionAuthMiddleware'])->group(function (){

    Route::get('/', function(){
        return view('SessionAuth.index');
    });

    #Rotas Assincronas

    Route::post('/set_session', function(Request $request){

        $request = json_decode($request->getContent());

        session(['SessionAuth'=>$request]);

        if (!session()->has('SessionAuth')) {
            return response()->json(['msg'=>'SessionAuth not found']);
        }else{
            return response()->json(['msg'=>'SessionAuth created']);
        }

    });

});

Route::get('/id_session', function(){

    if (session()->has('SessionAuth')) {
        return response()->json(['msg'=>'SessionAuth exists']);
    }

    $remote = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
    $id_session = MD5(config('SessionAuth.SESSION_ID_SYSTEM') . $remote );
    $id_system = config('SessionAuth.SESSION_ID_SYSTEM');


    if( empty($remote) || empty($id_session) || empty($id_system ) ){
        return response()->json(['error'=>'config to find SessionAuth not found']);
    }

    return response()->json(['id_session'=>$id_session, 'id_system'=> $id_system]);
});

Route::get('/logout', function(){
    session()->flush();
    return view('SessionAuth.index');
})->name('logout');


Route::get('/session', function(){
    if(env('APP_ENV') == 'local'){

        if (!session()->has('SessionAuth')) {
            return response()->json(['msg'=>'SessionAuth not found']);
        }

        return  dd( session('SessionAuth') );
    }

    return redirect()->route('home');

})->name('session');
