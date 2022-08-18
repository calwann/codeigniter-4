<?php

namespace App\Services;

use CodeIgniter\Config\BaseService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Response;
use Throwable;

class ExceptionService extends BaseService
{
    /**
     * Generate log
     *
     * @param Throwable $e
     * @return void
     */
    public static function generateLog(Throwable $e, string $level = 'error'): void
    {
        $message = "{$e->getMessage()}.\n{$e->getTraceAsString()}";
        log_message($level, $message);
    }

    /**
     * Return response json
     *
     * @param Throwable $e
     * @param array $data
     * @return Response
     */
    public static function responseJson(Throwable $e, array $data = []): Response
    {
        self::generateLog($e);

        if (empty($data)) {
            $data = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        $statusCode = $e->getCode() >= 100 && $e->getCode() < 600
            ? $e->getCode()
            : 500;

        $response = service('response');
        return $response->setJSON($data)->setStatusCode($statusCode);
    }

    /**
     * Return response redirect
     *
     * @param Throwable $e
     * @param array $data
     * @param string $uri
     * @return RedirectResponse
     */
    public static function responseRedirect(
        Throwable $e,
        array $data = [],
        string $uri = ''
    ): RedirectResponse {
        self::generateLog($e);

        $uri = $uri ?: '/';

        $redirect = redirect()->to($uri);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $redirect->with($key, $value);
            }
        }

        return $redirect;
    }
}
