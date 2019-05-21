<?php

use Wdgt\MenuBuilder\Models\Menu;
use Illuminate\Support\Facades\DB;

/**
 * get all menus (json)
 */
if (!function_exists('nova_get_menus')) {
    function nova_get_menus($name)
    {
        return Menu::with('rootMenuItems')
            ->get()
            ->map(function ($menu) {
                return $menu->formatForAPI();
            });
    }
}

/**
 * get menu fields (json) from slug
 */
if (!function_exists('nova_get_menu')) {
    function nova_get_menu_from_slug($menuSlug)
    {
        if (empty($menuSlug)) return null;

        $menu = Menu::where('slug', '=', $menuSlug)->get()->first();
        if (!isset($menu)) return null;

        return $menu->formatForAPI();
    }
}


/**
 * get menu fields (json) 
 */
if (!function_exists('nova_get_menu')) {
    function nova_get_menu($name, $language)
    {
        if (empty($name) || empty($language))
            return null;

        // $menu = Menu::where(DB::raw('LOWER(menus.name)'), strtolower($name))
        //     ->join('languages', 'menus.locale', '=', 'languages.id')
        //     ->where(DB::raw('LOWER(languages.code)'), strtolower($language))
        //     ->first();
        
        $menu = Menu::whereHas('language', function ($q) use ($language) {
                $q->where(DB::raw('LOWER(code)'), strtolower($language));
            })
            ->where(DB::raw('LOWER(name)'), strtolower($name))
            ->first();

        if (!isset($menu)) return null;

        return $menu->formatForAPI();
    }
}

