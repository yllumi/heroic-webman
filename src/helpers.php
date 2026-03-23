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

/**
 * Here is your custom functions.
 */

// ── Compat helpers for FormBuilder (ported from CI4) ─────────────────────
if (! function_exists('esc')) {
    function esc($data, string $context = 'html'): string
    {
        return htmlspecialchars((string) $data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (! function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        return $default; // No CI4 flash session in webman; always return default
    }
}

function admin_url($path = '')
{
    return '/app/panel/' . ltrim($path, '/');
}

function cell($view, $data = [])
{
    // Include the view file, parse data and capture the output
    ob_start();
    extract($data);
    include base_path() . '/plugin/panel/app/view/' . str_replace('.', '/', $view) . '.php';
    return ob_get_clean();
}

function render($view, $data = [], $layout = 'admin')
{
    $content = cell($view, $data);
    return view('_layouts/' . $layout, array_merge($data, ['content' => $content]));
}

function sidebarMenus()
{
    // Fetch menu items from the database
    $menus = \support\Db::table('panel_menu')
        ->orderBy('parent_id', 'asc')
        ->orderBy('order', 'asc')->get();

    // Build a nested menu structure
    $menuTree = [];
    foreach ($menus as $menu) {
        if ($menu->parent_id === null) {
            $menuTree[$menu->id] = [
                'label' => $menu->label,
                'module' => $menu->module,
                'icon' => $menu->icon,
                'url' => $menu->url,
                'target' => $menu->target,
            ];
        } else {
            if (isset($menuTree[$menu->parent_id])) {
                $menuTree[$menu->parent_id]['children'][] = [
                    'label' => $menu->label,
                    'module' => $menu->module,
                    'submodule' => $menu->submodule,
                    'icon' => $menu->icon,
                    'url' => $menu->url,
                    'target' => $menu->target
                ];
            }
        }
    }

    return $menuTree;
}

// $privilege feature.privilege, $whiteListID optional untuk memasukkan user_id yang boleh diloloskan
function isAllow($privilege, $whiteListIDs = [])
{
    // Check if user is in the white list
    $userId = session('user')['user_id'] ?? null;
    if ($userId && in_array($userId, $whiteListIDs)) {
        return true;
    }

    $role_id = session('user')['role_id'] ?? null;
    // Allow all privileges for role_id 1 (Super)
    if ($role_id == 1) {
        return true;
    }

    // Breakdown feature and privilege
    [$feature, $action] = explode('.', $privilege);
    
    // Check if user has the required privilege feature
    $userPrivileges = rolePrivileges($role_id ?? 0);
    if (isset($userPrivileges[$feature]) && in_array($action, $userPrivileges[$feature])) {
        return true;
    }

    return false;
}

function rolePrivileges($roleId)
{
    // Check from cache first
    $cacheKey = "role_privileges:{$roleId}";
    $cached = \support\Cache::get($cacheKey);
    if ($cached !== null) {
        return $cached;
    }

    $privileges = support\Db::table('mein_role_privileges')
        ->select('feature', 'privilege')
        ->where('role_id', $roleId)
        ->get()
        ->toArray();
    $privArray = [];
    foreach ($privileges as $priv) {
        $privArray[$priv->feature][] = $priv->privilege;
    }

    // Cache the result for 1 hour
    \support\Cache::set($cacheKey, $privArray, 3600);

    return $privArray;
}
