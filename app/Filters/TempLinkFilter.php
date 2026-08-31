<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\TempLinkService;
use CodeIgniter\Exceptions\PageNotFoundException;

class TempLinkFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // get token from second url segment
        $token = $request->getUri()->getSegment(2);
        if (!$token) {
            throw PageNotFoundException::forPageNotFound(); // Return 404 if no token
        }

        $service = new TempLinkService();
        $record = $service->validateToken($token);

        if (!$record) {
            // return not found page
            throw PageNotFoundException::forPageNotFound(); // Return 404 if no token
            // return redirect()->to(base_url("notFound"));
        }

        // Generate encrypted token to pass to original URL
        $encryptedToken = setKey($token, "tempLink");
        $url = rtrim($record['originalUrl'], "/");
        return redirect()->to($url . "/" . urlencode($encryptedToken));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No after logic needed
    }
}
