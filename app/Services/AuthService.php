<?php

namespace App\Services;

use Auth0\SDK\Auth0;
use CodeIgniter\Config\BaseService;
use Exception;

class AuthService extends BaseService
{
    /**
     * Instance Auth0
     *
     * @return Auth0
     * @throws Exception
     */
    private static function _getAuth(): Auth0
    {
        if (
            !env('auth0.domain')
            || !env('auth0.clientId')
            || !env('auth0.clientSecret')
        ) {
            throw new Exception('Undeclared Auth0 environments.', 500);
        }

        return new Auth0([
            'domain' => env('auth0.domain'),
            'clientId' => env('auth0.clientId'),
            'clientSecret' => env('auth0.clientSecret'),
            'cookieSecret' => env('auth0.cookieSecret'),
        ]);
    }

    /**
     * Get Session
     *
     * @return object|null
     */
    public static function getSession(): ?object
    {
        $auth = self::_getAuth();

        return $auth->getCredentials();
    }

    /**
     * Get User
     *
     * @return array|null
     */
    public static function getUser(): ?array
    {
        $session = self::getSession();

        if (!$session) {
            return null;
        }

        return $session->user;
    }

    /**
     * Logging in 
     *
     * @param string $callback
     * @return string
     */
    public static function login(string $callback = ''): string
    {
        $auth = self::_getAuth();

        if (!$callback) {
            $baseUrl = env('app.baseURL');
            $callback = "{$baseUrl}login/callback";
        }

        $auth->clear();

        return $auth->login($callback);
    }

    /**
     * Handling authentication callback
     *
     * @param string $callback
     * @param string $redirect
     * @return string
     * @throws Exception
     */
    public static function callback(string $callback, string $redirect = ''): string
    {
        $auth = self::_getAuth();

        $exchange = $auth->exchange($callback);
        if (!$exchange) {
            throw new Exception("Logging in authentication failed", 500);
        }

        return $redirect ?: env('app.baseURL');
    }

    /**
     * Logging out
     *
     * @param string $redirect
     * @return string
     */
    public static function logout(string $redirect = ''): string
    {
        $auth = self::_getAuth();

        if (!$redirect) {
            $redirect = env('app.baseURL');
        }

        return $auth->logout($redirect);
    }
}
