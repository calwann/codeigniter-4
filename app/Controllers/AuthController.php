<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Services\ExceptionService;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class AuthController extends BaseController
{
    /**
     * Redirect to login
     *
     * @return RedirectResponse
     */
    public function login(): RedirectResponse
    {
        try {
            $url = AuthService::login();
        } catch (Throwable $e) {
            return ExceptionService::responseRedirect($e, [
                'msg' => 'Login failed'
            ]);
        }

        return redirect()->to($url);
    }

    /**
     * After log in redirect to website
     *
     * @return RedirectResponse
     */
    public function loginCallback(): RedirectResponse
    {
        $baseUrl = env('app.baseURL');
        $callback = "{$baseUrl}login/callback";

        try {
            $url = AuthService::callback($callback);
        } catch (Throwable $e) {
            return ExceptionService::responseRedirect($e, [
                'msg' => 'Login failed'
            ]);
        }

        return redirect()->to($url);
    }

    /**
     * Log out and redirect to website
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        try {
            $url = AuthService::logout();
        } catch (Throwable $e) {
            return ExceptionService::responseRedirect($e, [
                'msg' => 'Logout failed'
            ]);
        }

        return redirect()->to($url);
    }
}
