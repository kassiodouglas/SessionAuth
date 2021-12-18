<?php

namespace App\Http\Controllers\SessionAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionAuthController extends Controller
{
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
        if(config('app.env') == 'local')
            return SessionAuth();

        return false;
    }


}
