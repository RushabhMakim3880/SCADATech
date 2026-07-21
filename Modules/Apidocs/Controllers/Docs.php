<?php

namespace Modules\Apidocs\Controllers;

use CodeIgniter\Controller;

class Docs extends Controller
{

    public function __construct()
    {
        $config = new \Config\AppConfig();
        if (!$config->apiDocumentation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("API documentation is not available");
        }
    }

    public function spec()
    {
        // Get all directories to scan
        $directories = [ROOTPATH . 'Modules/Backend'];

        // Use recursive directory iterator to filter files
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directories[0], \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (str_contains($file->getPathname(), '/Controllers/')) { // Skip Database folders
                $files[] = $file->getPathname();
            }
        }

        // Use the global scan function to scan for annotations
        $openapi = \OpenApi\Generator::scan($files);

        // die(ROOTPATH . 'Modules/Backend/Users/Controllers');

        // Set content type to JSON and output the spec
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setBody($openapi->toJson());
    }

    /**
     * Serve the Swagger UI for interactive documentation.
     */
    public function index()
    {
        // Load a view that embeds Swagger UI
        return view('\Modules\Apidocs\Views\doc');
    }
}
