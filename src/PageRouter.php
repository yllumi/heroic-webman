<?php

namespace Yllumi\HeroicWebman;

class PageRouter
{
    /**
     * Inisialisasi routing berbasis halaman
     */
    public static function init()
    {
        \Webman\Route::fallback(function (\support\Request $request) {
            // Gunakan static agar cache instansi bertahan di memori worker
            static $instances = [];

            // Pre-load NotFound Controller jika belum ada
            if (!isset($instances['notfound'])) {
                $instances['notfound'] = new \app\pages\notfound\PageController;
            }

            // Gunakan path() bukan uri() untuk menghindari query string masuk ke pengecekan file
            $path = trim($request->path(), '/');
            $httpVerb = strtolower($request->method());

            if ($path === '') {
                $path = config('app.default_page', 'home');
            }

            $pagesPath      = config('app.pages_path', app_path('pages'));
            $controllerName = 'PageController';
            $methodName     = $httpVerb . 'Index';
            $params         = [];

            $uriSegments = explode('/', $path);

            // Loop untuk mencari folder terdalam yang memiliki PageController.php
            while ($uriSegments !== []) {
                $subPath = implode('/', $uriSegments);
                $folderPath = $pagesPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subPath);

                if (is_dir($folderPath) && file_exists($folderPath . DIRECTORY_SEPARATOR . $controllerName . '.php')) {

                    $controllerNamespace = "app\\pages\\" . str_replace('/', '\\', $subPath) . "\\$controllerName";
                    $params = array_reverse($params);

                    // Tentukan Method: apakah itu method spesifik (GET/POST + Action) atau Index
                    if (isset($params[0]) && method_exists($controllerNamespace, $httpVerb . ucfirst($params[0]))) {
                        $methodName = $httpVerb . ucfirst($params[0]);
                        array_shift($params);
                    } elseif (!method_exists($controllerNamespace, $methodName)) {
                        // Method Index tidak ada, lempar ke NotFound
                        return $instances['notfound']->getIndex($request);
                    }

                    // Cache instansi Controller agar tidak 'new' terus setiap request
                    if (!isset($instances[$controllerNamespace])) {
                        if (!class_exists($controllerNamespace)) {
                            return $instances['notfound']->getIndex($request);
                        }
                        $instances[$controllerNamespace] = new $controllerNamespace;
                    }

                    // Panggil method dengan menyuntikkan Request sebagai parameter pertama, diikuti params URL
                    // Ini standar Webman agar controller tetap bisa akses $request
                    return $instances[$controllerNamespace]->{$methodName}($request, ...$params);
                }

                $params[] = array_pop($uriSegments);
            }

            return $instances['notfound']->getIndex($request);
        });
    }

    public static function scanFrontendRouters($pagesPath = null)
    {
        $pagesPath = $pagesPath ?? config('app.pages_path', app_path('pages'));
        $router = [];

        // Scan pagePath recursively
        $directory = new \RecursiveDirectoryIterator($pagesPath);
        $iterator = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            // Get pathname starts from pages folder
            $folderPath = str_replace($pagesPath . DIRECTORY_SEPARATOR, '', $file->getPath());

            // Exclude page folder with prefix underscore
            if (
                strpos($folderPath, '_') === 0
                || strpos($folderPath, DIRECTORY_SEPARATOR . '_') !== false
            ) {
                continue;
            }

            // Only process page with PageController.php files
            if ($file->isFile() && $file->getFilename() === 'PageController.php') {
                $relativePath = str_replace($pagesPath . DIRECTORY_SEPARATOR, '', $file->getPath());
                $namespacePath = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
                $controllerClass = "\\app\\pages\\$namespacePath\\PageController";

                if (class_exists($controllerClass)) {
                    $reflectionClass = new \ReflectionClass($controllerClass);
                    $classAttributes = $reflectionClass->getAttributes(FrontendRoute::class);
                    foreach ($classAttributes as $attribute) {
                        $instance = $attribute->newInstance();

                        // Jika route tidak di-set, gunakan path berdasarkan folder
                        if (empty($instance->route)) {
                            $instance->route = '/' . trim(str_replace(DIRECTORY_SEPARATOR, '/', $relativePath), '/');
                        }

                        $router[$instance->route] = [
                            'template' => $instance->template ? $instance->template : $instance->route . '/template',
                            'preload'  => $instance->preload,
                            'handler'  => $instance->handler,
                        ];
                    }
                }
            }
        }

        // Urutkan router berdasarkan path yang lebih spesifik
        uksort($router, function ($a, $b) {
            $hasParamA = strpos($a, ':') !== false;
            $hasParamB = strpos($b, ':') !== false;
            
            // Dahulukan path dengan parameter
            if ($hasParamA && !$hasParamB) {
                return -1;
            }
            if (!$hasParamA && $hasParamB) {
                return 1;
            }
            
            // Jika sama-sama punya/tidak punya parameter, urutkan berdasarkan jumlah segment
            $segmentsA = count(explode('/', trim($a, '/')));
            $segmentsB = count(explode('/', trim($b, '/')));
            return $segmentsB - $segmentsA;
        });

        return $router;
    }

    public static function renderRouter($router = [], $minify = false)
    {
        $routerString = "";

        foreach ($router as $route => $routeProp) {
            $routePath = is_string($routeProp) ? $routeProp : $route;
            $routerString .= "<template \nx-route=\"{$routePath}\" \n";

            // Siapkan nilai template
            $templateStr = "";
            $hasParamInTemplate = false;

            if (isset($routeProp['template'])) {
                if (is_array($routeProp['template'])) {
                    $templateStr = str_replace(['"', '\/'], ["'", '/'], json_encode($routeProp['template']));
                    $hasParamInTemplate = preg_match('/:([^\/]+)/', implode('', $routeProp['template']));
                } else {
                    $templateStr = $routeProp['template'];
                    $hasParamInTemplate = preg_match('/:([^\/]+)/', $templateStr);
                }
            } else {
                // Default template jika tidak disediakan
                $cleanedPath = "/" . trim(preg_replace('/:([^\/]+)/', '', $routePath), '/');
                $cleanedPath = $cleanedPath === '/' ? '/home' : $cleanedPath;
                $templateStr = $cleanedPath . "/template";
            }

            $routerString .= "x-template"
                . (($routeProp['preload'] ?? false) ? ".preload" : "")
                . ($hasParamInTemplate ? ".interpolate" : "")
                . "=\"{$templateStr}\" \n";

            if (isset($routeProp['handler']) && !empty($routeProp['handler'])) {
                $handlerString = is_array($routeProp['handler']) ? implode(',', $routeProp['handler']) : $routeProp['handler'];
                $routerString .= "x-handler=\"" . $handlerString . "\"";
            }

            $routerString .= "></template>\n\n";
        }

        return $minify ? str_replace("\n", "", $routerString) : $routerString;
    }
}
