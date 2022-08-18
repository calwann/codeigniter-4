<?php

namespace App\Services;

use CodeIgniter\Config\BaseService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ApiService extends BaseService
{
    public const TIMEOUT = 25;
    public const CONNECT_TIMEOUT = 20;

    /**
     * Create curl request with Guzzle
     * @see https://docs.guzzlephp.org/en/stable/
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @return object|null
     * @throws Exception
     */
    public function request(string $method, string $url, array $options = []): ?object
    {
        try {
            $client = new Client(['timeout' => self::TIMEOUT]);

            if (empty($options['connect_timeout'])) {
                $options['connect_timeout'] = self::CONNECT_TIMEOUT;
            }

            $response = $client->request($method, $url, $options);
        } catch (GuzzleException $e) {
            if ($e->getCode() === 404) {
                log_message('info', "{$method} {$url}, 404");
                return null;
            }

            ExceptionService::generateLog($e);

            throw new Exception($e->getMessage(), $e->getCode(), $e);
        } catch (Throwable $th) {
            if ($th->getCode() === 404) {
                log_message('info', "{$method} {$url}, 404");
                return null;
            }

            ExceptionService::generateLog($th);

            throw $th;
        }

        log_message('info', "{$method} {$url}, {$response->getStatusCode()}");

        return self::_getContents($response);
    }

    /**
     * Get contents 
     *
     * @param ResponseInterface $response
     * @return object|null
     */
    private static function _getContents(ResponseInterface $response): ?object
    {
        $contents = $response->getBody()->getContents();

        if (empty($contents)) {
            return null;
        }

        if (in_array('application/pdf', $response->getHeader("Content-Type"))) {
            $document = (object) [];
            $document->pdf = $contents;
            return $document;
        }

        return (object) json_decode($contents);
    }

    /**
     * Make header to download attachment
     *
     * @param string $name
     * @param string $extension
     * @return void
     */
    public function attachmentHeader(string $name, string $extension): void
    {
        $date = date('Y-m-d-Hms');
        $gmdate = gmdate('D, d M Y H:i:s');

        header("Content-Disposition: attachment;filename=\"{$name}-{$date}.{$extension}\"");
        header("Cache-Control: max-age=0");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header("Last-Modified: {$gmdate} GMT"); // always modified
        header("Cache-Control: cache, must-revalidate"); // HTTP/1.1
        header("Pragma: public"); // HTTP/1.0
    }
}
