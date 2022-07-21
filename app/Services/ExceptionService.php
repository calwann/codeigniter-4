<?php

namespace App\Services;

use CodeIgniter\Config\BaseService;
use CodeIgniter\HTTP\Response;
use Throwable;

class ExceptionService extends BaseService
{
    /**
     * Return response json
     *
     * @param Throwable $e
     * @param array $data
     * @return Response
     */
    public static function reponseJson(Throwable $e, array $data = []): Response
    {
        log_message('error', 'Exception error', ['exception' => $e]);

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
}
