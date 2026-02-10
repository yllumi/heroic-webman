<?php

/**
 * Helper functions for Heroic Webman
 */

// Include a partial view
// View file must be located in app/pages folder 
function partial($view, $data = [])
{
    extract($data);
    include base_path() . '/app/pages/' . str_replace('.', '/', $view) . '.php';
}

// Render a page view and return its content
function pageView($view, $data = [])
{
    extract($data);
    ob_start();
    include base_path() . '/app/pages/' . str_replace('.', '/', $view) . '.php';
    return ob_get_clean();
}

// Generate asset URL with versioning based on file modification time
function asset_url(string $filePath): string
{
    // Full file path
    $fullFilePath = './public/' . $filePath;

    // Check if file exists
    $version = file_exists($fullFilePath)
        // Add file modification time as version
        ? filemtime($fullFilePath)
        // Fallback version (current timestamp if file doesn't exist)
        : time();

    // Generate full URL with version
    return $filePath . '?v=' . $version;
}

if (!function_exists('base_url')) {
    function base_url($path = '') {
        return config('app.url') . ltrim($path, '/');
    }
}

// Dump data in browser friendly way for webman
if (!function_exists('ddb')) {
    function ddb(...$vars)
    {
        $cloner = new \Symfony\Component\VarDumper\Cloner\VarCloner();
        $dumper = new \Symfony\Component\VarDumper\Dumper\HtmlDumper();
        $output = fopen('php://memory', 'r+b');

        // If only one parameter, dump it directly instead of as an array
        $data = count($vars) === 1 ? $vars[0] : $vars;

        $dumper->dump($cloner->cloneVar($data), $output);
        rewind($output);
        $result = stream_get_contents($output);
        return response($result);
    }
}

// Collection to array helper
if (!function_exists('to_assoc')) {
    function to_assoc($collection)
    {
        return array_map(function ($item) {
            return (array) $item;
        }, is_array($collection) ? $collection : $collection->all());
    }
}
