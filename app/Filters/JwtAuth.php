<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\Config\Services;
use App\Libraries\Auth;

class JwtAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {

        $uri = ltrim($request->getUri()->getPath(), '/');
        // die($uri);

        if (!Auth::check() || !Auth::verifySingleSignOn()) {
            return $this->handleUnauthorized($uri);
        }


        // $authHeader = $request->getHeader('Authorization');

        // if (!$authHeader) {
        //     return Services::response()
        //         ->setJSON(['error' => 'Authorization header missing'])
        //         ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        // }

        // $authHeader = $authHeader->getValue();
        // if (strpos($authHeader, 'Bearer ') !== 0) {
        //     return Services::response()
        //         ->setJSON(['error' => 'Invalid authorization format'])
        //         ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        // }

        // $token = substr($authHeader, 7);
        // $decoded = verifyJWT($token);

        // if (!$decoded) {
        //     return Services::response()
        //         ->setJSON(['error' => 'Invalid or expired token'])
        //         ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        // }

        // // Optionally, set user data to request for later use
        // $request->userData = $decoded->data;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }

    private function handleUnauthorized(string $uri)
    {
        if (str_starts_with($uri, 'api/')) {
            return Services::response()->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $isAjax = (isset($_SERVER['HTTP_X_CLIENT_TYPE']) && strtolower($_SERVER['HTTP_X_CLIENT_TYPE']) === 'web');
        if ($isAjax) {
            return Services::response()->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $prevUrl = current_url(true);
        // $url = str_replace("index.php/", '', $url);

        return redirect()->to('/auth/login?redirect=' . urlencode($prevUrl)); // Redirect for frontend users
    }
}
