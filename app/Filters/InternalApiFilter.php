<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class InternalApiFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $expectedToken = getenv('internalApiToken');
        $incomingToken = $request->getHeaderLine('X-Internal-Token');
        if ($incomingToken == "" and isset($_GET['internalApiToken'])) {
            $incomingToken = $_GET['internalApiToken'];
        }

        if ($incomingToken !== $expectedToken) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized internal access']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
