<?php

namespace App\Libraries;

use MatthiasMullie\Minify;

class AssetManager
{
    // Global arrays to store asset paths
    protected static $custom_css = [];
    protected static $custom_js  = [];
    protected static $cdnCss = [];
    protected static $cdnJs  = [];
    public static $clientType = 'desktop';

    // Registry of predefined libraries and their assets
    protected static $libraries = [
        'echarts' => [
            'css' => [],
            'js'  => ['https://cdnjs.cloudflare.com/ajax/libs/echarts/5.6.0/echarts.min.js']
        ],
        'SweetAlert2' => [
            'css' => ['https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css'],
            'js'  => ['https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js']
        ],
        'Toastr' => [
            'css' => ['https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css'],
            'js'  => ['https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js']
        ],
        'Notyf' => [
            'css' => ['https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css'],
            'js'  => ['https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js']
        ],
        'Select2' => [
            'css' => ['https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css'],
            'js'  => ['https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js']
        ],
        'DatePicker' => [
            'css' => ['https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css'],
            'js'  => [
                'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js',
                'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js'
            ]
        ],
        'ColorPicker' => [
            'css' => ['https://cdn.jsdelivr.net/npm/spectrum-colorpicker2/dist/spectrum.min.css'],
            'js'  => ['https://cdn.jsdelivr.net/npm/spectrum-colorpicker2/dist/spectrum.min.js']
        ],
        'IconPicker' => [
            'css' => [],
            'js'  => ['mtplPlugins/iconPicker/iconPicker.js']
        ],
        'DataTables' => [
            'css' => [
                'https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css',
                'https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.bootstrap5.min.css',
            ],
            'js'  => [
                'https://cdn.datatables.net/2.2.1/js/dataTables.min.js',
                'https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js',
                'https://cdn.datatables.net/colreorder/2.0.4/js/dataTables.colReorder.min.js',
                'https://cdn.datatables.net/colreorder/2.0.4/js/colReorder.bootstrap5.min.js',
                'assets/js/manageDatatables.js',
                // 'https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js',
                // 'https://cdn.datatables.net/responsive/3.0.4/js/responsive.bootstrap5.js'
            ],
        ],
        "GridStack" => [
            'css' => [
                "https://cdn.jsdelivr.net/npm/gridstack@11.3.0/dist/gridstack.min.css",
                "https://cdn.jsdelivr.net/npm/gridstack@11.3.0/dist/gridstack-extra.min.css"
            ],
            'js' => ["https://cdn.jsdelivr.net/npm/gridstack@11.3.0/dist/gridstack-all.min.js"],
        ],
        'ImageUpload' => [
            'css' => [
                'mtplPlugins/image_upload/cropper.css',
            ],
            'js'  => [
                'mtplPlugins/image_upload/cropper.js',
                'mtplPlugins/image_upload/image.js',
                'mtplPlugins/image_upload/webcam.js',
            ],
        ],
        "Tippy" => [
            'css' => [
                "https://cdn.jsdelivr.net/npm/tippy.js@6.3.7/dist/tippy.min.css",
            ],
            'js' => [
                "https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js",
                "https://cdn.jsdelivr.net/npm/tippy.js@6.3.7/dist/tippy.umd.min.js",
            ],
        ],
        "Sortable" => [
            'css' => [],
            'js' => [
                "https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js",
            ],
        ],
        "InternationalNumber" => [
            'css' => [
                "https://cdn.jsdelivr.net/npm/intl-tel-input@24.4.0/build/css/intlTelInput.css",
            ],
            'js' => [
                "https://cdn.jsdelivr.net/npm/intl-tel-input@24.4.0/build/js/intlTelInput.js",
                // "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/utils.js",
            ],
        ],
        "LocationPicker" => [
            'css' => [
                "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css",
                "https://cdn.jsdelivr.net/npm/leaflet-geosearch@3.0.0/dist/geosearch.css",
            ],
            'js' => [
                "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js",
                "https://unpkg.com/leaflet-geosearch@4.2.0/dist/bundle.min.js",
                "mtplPlugins/locationPicker/locationPicker.js",
            ],
        ],
        'Barcode' => [
            'css' => [],
            'js'  => [
                'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js',
                'assets/js/QrScanner.js',
            ]
        ],
        'HtmlEditor' => [
            'css' => [
                'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css',
            ],
            'js'  => [
                'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js',
            ]
        ],
        'slideShow' => [
            'css' => [
                'assets/css/imageSlideShow.css',
            ],
            'js'  => [
                'assets/js/imageSlideShow.js',
            ]
        ],
        'konva' => [
            'css' => [],
            'js'  => [
                'https://unpkg.com/konva@9/konva.min.js'
            ]
        ],
        'virtualNumKeypad' => [
            'css' => [
                'mtplPlugins/virtualNumKeypad/virtualNumKeypad.css',
            ],
            'js'  => [
                'mtplPlugins/virtualNumKeypad/virtualNumKeypad.js',
            ]
        ]
        // Add more predefined libraries as needed
    ];

    protected static $librariesMobile = [
        'DataTables' => [
            'css' => [],
            'js'  => ['assets/js/manageDatatablesMobile.js']
        ],
    ];

    /**
     * Add a CSS file to the global list.
     */
    public static function addCss(string $path)
    {
        if (!in_array($path, self::$custom_css)) {
            self::$custom_css[] = $path;
        }
    }

    public static function addcdnCss(string $path)
    {
        if (!in_array($path, self::$cdnCss)) {
            self::$cdnCss[] = $path;
        }
    }

    public static function addcdnJs(string $path)
    {
        if (!in_array($path, self::$cdnJs)) {
            self::$cdnJs[] = $path;
        }
    }

    /**
     * Add a JS file to the global list.
     */
    public static function addJs(string $path)
    {
        if (!in_array($path, self::$custom_js)) {
            self::$custom_js[] = $path;
        }
    }

    /**
     * Load a predefined library's assets.
     */
    public static function loadLibrary(string $name)
    {
        $config = config('AppConfig');

        if ($name == "echarts") {
            $theme = $config->chartTheme;
            if ($theme != 'default') {
                self::$libraries[$name]['js'][] = 'https://cdnjs.cloudflare.com/ajax/libs/echarts/5.6.0/theme/' . $theme . '.min.js';
            }
        }

        if (isset(self::$libraries[$name])) {

            if (self::$clientType === 'mobile' && isset(self::$librariesMobile[$name])) {
                $cssLibraries = self::$librariesMobile[$name]['css'] ?? [];
                $jsLibraries = self::$librariesMobile[$name]['js'] ?? [];
            } else {
                $cssLibraries = self::$libraries[$name]['css'] ?? [];
                $jsLibraries = self::$libraries[$name]['js'] ?? [];
            }

            foreach ($cssLibraries as $css) {
                if (strpos($css, 'http') === false) {
                    self::addCss($css);
                } else {
                    // self::saveLocal($name, $css, 'css');

                    if ($config->cdnToLocal) {
                        $css = self::cdnToLocalPath($name, $css, 'css');
                        if ($css) {
                            self::addCss($css);
                        } else {
                            self::addcdnCss($css);
                        }
                    } else {
                        self::addcdnCss($css);
                    }
                }
            }
            foreach ($jsLibraries as $js) {
                if (strpos($js, 'http') === false) {
                    self::addJs($js);
                } else {
                    // self::saveLocal($name, $js, 'js');
                    if ($config->cdnToLocal) {
                        $js = self::cdnToLocalPath($name, $js, 'js');
                        if ($js) {
                            self::addJs($js);
                        } else {
                            self::addcdnJs($js);
                        }
                    } else {
                        self::addcdnJs($js);
                    }
                }
            }
        }
    }

    /**
     * Retrieve all added CSS files.
     */
    public static function getCss(): array
    {
        return self::$custom_css;
    }

    public static function getcdnCss(): array
    {
        return self::$cdnCss;
    }

    public static function getcdnJs(): array
    {
        return self::$cdnJs;
    }

    /**
     * Retrieve all added JS files.
     */
    public static function getJs(): array
    {
        return self::$custom_js;
    }

    public static function combinedCssToken(): string
    {
        $files = self::$custom_css;
        $first = true;

        $hash = md5(implode(',', $files));

        foreach ($files as $file) {
            $absolutePath = FCPATH . $file;
            if (file_exists($absolutePath)) {
                if ($first) {
                    $minifier = new Minify\CSS($absolutePath);
                    $first = false;
                } else {
                    $minifier->add($absolutePath);
                }
            } else {
                die($absolutePath);
            }
        }

        // save minified file to disk
        $minifiedPath = WRITEPATH . 'cache/combined_' . $hash . '.css';
        if (isset($minifier))
            $minifier->gzip($minifiedPath);

        return $hash;


        // $serialized = base64_encode(json_encode(self::$custom_css));
        // $encrypted = encryptData($serialized);
        // $token = urlencode(base64_encode($encrypted));

        // $lastModifierToken = self::lastModifierToken(self::$custom_css);

        // return [
        //     'token' => $token,
        //     'lastModifierToken' => $lastModifierToken
        // ];
    }

    public static function combinedJsToken(): string
    {
        $files = self::$custom_js;
        $first = true;

        $hash = md5(implode(',', $files));

        foreach ($files as $file) {
            $absolutePath = FCPATH . $file;
            if (file_exists($absolutePath)) {
                if ($first) {
                    $minifier = new Minify\JS($absolutePath);
                    $first = false;
                } else {
                    $minifier->add($absolutePath);
                }
            }
        }

        // save minified file to disk
        $minifiedPath = WRITEPATH . 'cache/combined_' . $hash . '.js';
        $minifier->gzip($minifiedPath);

        return $hash;

        // $serialized = base64_encode(json_encode(self::$custom_js));
        // $encrypted = encryptData($serialized);
        // $token = urlencode(base64_encode($encrypted));

        // $lastModifierToken = self::lastModifierToken(self::$custom_js);

        // return [
        //     'token' => $token,
        //     'lastModifierToken' => $lastModifierToken
        // ];
    }

    private static function lastModifierToken($files): string
    {
        $lastModifiedAggregate = '';
        foreach ($files as $file) {
            $absolutePath = FCPATH . $file;
            if (file_exists($absolutePath)) {
                $lastModifiedAggregate .= filemtime($absolutePath);
            }
        }

        return md5($lastModifiedAggregate);
    }

    private static function cdnToLocalPath($library, $file, $type)
    {

        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $hash = md5($file);
        $absolutePath = FCPATH . 'cdnToLocal/' . $library . '/' . $fileName . '_' . $hash . '.' . $type;

        if (file_exists($absolutePath)) {
            return 'cdnToLocal/' . $library . '/' . $fileName . '_' . $hash . '.' . $type;
        }
        return $file;
    }

    public static function autoSyncIfDev()
    {
        $env = getenv('CI_ENVIRONMENT');

        if ($env === 'development') {
            self::syncAllCdnToLocal();
        }
    }

    public static function syncAllCdnToLocal()
    {
        $allLibraries = array_merge(self::$libraries, self::$librariesMobile);

        foreach ($allLibraries as $library => $files) {
            foreach (['css', 'js'] as $type) {
                if (isset($files[$type])) {
                    foreach ($files[$type] as $file) {
                        if (strpos($file, 'http') === false) {
                            continue; // Skip local paths
                        }
                        self::saveLocal($library, $file, $type);
                    }
                }
            }
        }
    }


    private static function saveLocal($library, $file, $type)
    {
        umask(0002);
        $fileName = pathinfo($file, PATHINFO_FILENAME);

        $hash = md5($file);

        $absolutePath = FCPATH . 'cdnToLocal/' . $library . '/' . $fileName . '_' . $hash . '.' . $type;

        if (file_exists($absolutePath)) {
            return;
        }

        $path = pathinfo($absolutePath, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }

        $content = file_get_contents($file);
        file_put_contents($absolutePath, $content);
    }

    public static function loadCssAssets()
    {
        $cdnCss = self::getcdnCss();
        $localCss = self::getCss();
        $config = config('AppConfig');

        if (!empty($cdnCss)) {
            foreach ($cdnCss as $css) {
                echo '<link href="' . $css . '" rel="stylesheet" />' . "\n";
            }
        }
        if ($config->combinedAssets && getenv('CI_ENVIRONMENT') === 'production') {
            $hash = self::combinedCssToken();
            echo '<link href="' . base_url("assets/combined.css?key=" . $hash) . '" rel="stylesheet" />';
        } else {
            foreach ($localCss as $css) {
                echo '<link href="' . versioned_asset($css) . '" rel="stylesheet" />' . "\n";
            }
        }
    }

    public static function loadJsAssets()
    {
        $cdnJs = self::getcdnJs();
        $localJs = self::getJs();
        $config = config('AppConfig');

        if (!empty($cdnJs)) {
            foreach ($cdnJs as $js) {
                echo '<script src="' . $js . '"></script>' . "\n";
            }
        }

        if ($config->combinedAssets && getenv('CI_ENVIRONMENT') === 'production') {
            $hash = self::combinedJsToken();
            echo '<script src="' . base_url("assets/combined.js?key=" . $hash) . '"></script>';
        } else {
            foreach ($localJs as $js) {
                echo '<script src="' . versioned_asset($js) . '"></script>' . "\n";
            }
        }
    }
}
