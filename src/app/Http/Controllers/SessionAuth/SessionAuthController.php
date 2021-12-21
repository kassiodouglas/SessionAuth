<?php

namespace App\Http\Controllers\SessionAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionAuthController extends Controller
{
    /**
     * Redireciona para a página de inicial com name de home
     *
     * @return void
     */
    public function home()
    {
        return redirect()->route('home');
    }

    /**
     * Redireciona para a página de logout
     *
     * @return void
     */
    public function logout()
    {
        return redirect(SessionAuthRouteLogout());
    }

    /**
     * Redireciona para a página de login
     *
     * @return void
     */
    public function login()
    {
        return redirect(SessionAuthRouteLogin());
    }

    /**
     * Exibe os dados da sessão
     *
     * @return void
     */
    public function session()
    {        
        return SessionAuth();       
    }


}
