<?php
/**
 * Created by PhpStorm.
 * User: sergeytangyan
 * Date: 12/18/17
 * Time: 12:41 PM
 */

namespace AppBundle\ApiUtils;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    private $statusCode;
    private $body;

    public function __construct(string $message, int $statusCode = Response::HTTP_CONFLICT, $body = null)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        parent::__construct($statusCode, $message, null);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }
}