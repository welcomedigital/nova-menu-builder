<?php

namespace WelcomeDigital\MenuBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use WelcomeDigital\MenuBuilder\Models\MenuItem;

class Menu extends Model
{
    public function rootMenuItems()
    {
        return $this
            ->hasMany(MenuItem::class)
            ->where('parent_id', null)
            ->orderby('parent_id')
            ->orderby('order')
            ->orderby('name');
    }

    public function formatForAPI()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'locale' => $this->locale,
            'menuItems' => collect($this->rootMenuItems)->map(function ($item) {
                return $this->formatMenuItem($item);
            }),
        ];
    }

    public function formatMenuItem($menuItem)
    {
        return [
            'id' => $menuItem->id,
            'name' => $menuItem->name,
            'type' => $menuItem->type,
            'value' => $menuItem->customValue,
            'target' => $menuItem->target,
            'parameters' => $menuItem->parameters,
            'enabled' => $menuItem->enabled,
            'children' => empty($menuItem->children) ? [] : $menuItem->children->map(function ($item) {
                return $this->formatMenuItem($item);
            }),
        ];
    }
}
