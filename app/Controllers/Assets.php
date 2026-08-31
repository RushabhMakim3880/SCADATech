<?php

namespace App\Controllers;

// use App\Libraries\AssetManager;

class Assets extends BaseController
{
    public function css()
    {

        $hash = $this->request->getGet('key');
        if (!$hash) {
            // Handle error
        }

        $file = WRITEPATH . 'cache/combined_' . $hash . '.css';

        if (!file_exists($file)) {
            // return 404
            return $this->response->setStatusCode(404);
        }

        $content = file_get_contents($file);

        // Serve the combined CSS
        return $this->response
            ->setHeader('Content-Type', 'text/css')
            ->setHeader('Content-Encoding', 'gzip')
            ->removeHeader('Cache-Control')  // Remove any existing cache-control headers
            ->setHeader('Cache-Control', 'public, max-age=31536000, immutable')
            ->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT')
            ->setBody($content);
    }

    public function js()
    {
        $hash = $this->request->getGet('key');
        if (!$hash) {
            // Handle error
        }

        $file = WRITEPATH . 'cache/combined_' . $hash . '.js';

        if (!file_exists($file)) {
            // return 404
            return $this->response->setStatusCode(404);
        }

        $content = file_get_contents($file);

        return $this->response
            ->setHeader('Content-Type', 'application/javascript')
            ->setHeader('Content-Encoding', 'gzip')
            ->removeHeader('Cache-Control')  // Remove any existing cache-control headers
            ->setHeader('Cache-Control', 'public, max-age=31536000, immutable')
            ->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT')
            ->setBody($content);
    }

    public function manifest()
    {
        $config = config('AppConfig');

        $manifestData = [
            "name" => $config->appName ?: "My Web App",
            "short_name" => $config->appShortName ?: "My Web App",
            "start_url" => "/",
            "display" => "standalone",
            "background_color" => "#ffffff",
            "theme_color" => "#000000",
            "icons" => [
                [
                    "src" => getFaviconShortUrl(),
                    "sizes" => "192x192",
                    "type" => "image/png",
                ],
                [
                    "src" => getFaviconUrl(),
                    "sizes" => "512x512",
                    "type" => "image/png",
                ]
            ],
        ];

        $response = $this->response;
        $response->setContentType('application/json');
        $response->removeHeader('Cache-Control');  // Remove any existing cache-control headers
        $response->setHeader('Cache-Control', 'public, max-age=31536000, immutable');
        $response->setHeader('Pragma', 'cache'); // For compatibility with HTTP/1.0
        $response->setJSON($manifestData);

        return $response;
    }



    /**
     * Basic CSS minification: remove comments and excess whitespace.
     */
    private function minifyCss(string $file, string $css): string
    {
        //disabled trim as it creates issue with some css due to // comments
        $css = "\n/*===============================================*/\n/*" . $file . "*/\n/*===============================================*/\n" . $css;
        return trim($css);

        // Remove comments
        $css = preg_replace('!/\*.*?\*/!s', '', $css);
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        // Remove spaces around symbols
        $css = preg_replace('/\s*([{};:>,])\s*/', '$1', $css);
        return trim($css);
    }

    /**
     * Basic JS minification: remove comments and excess whitespace.
     */

    private function minifyJs(string $file, string $js): string
    {
        // disabled trim as it creates issue with some js due to // comments
        $js = "\n/*===============================================*/\n/*" . $file . "*/\n/*===============================================*/\n" . $js;
        return trim($js);

        // Remove multi line comments like /* */
        $js = preg_replace('!/\*.*?\*/!s', '', $js);
        // Remove whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        // Remove spaces around symbols
        $js = preg_replace('/\s*([{};:>,])\s*/', '$1', $js);
        return trim($js);
    }
}
