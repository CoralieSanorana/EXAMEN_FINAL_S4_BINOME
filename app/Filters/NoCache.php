<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class NoCache implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // No action needed before
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Set cache control headers to prevent browser caching
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                 ->setHeader('Cache-Control', 'post-check=0, pre-check=0', false)
                 ->setHeader('Pragma', 'no-cache')
                 ->setHeader('Expires', 'Wed, 11 Jan 1984 05:00:00 GMT');
    }
}
