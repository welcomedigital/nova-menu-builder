<?php

namespace Wdgt\MenuBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Wdgt\MenuBuilder\Models\MenuItem;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Cviebrock\EloquentSluggable\Sluggable as Sluggable;


class Menu extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['title','slug'];

    use Sluggable;
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['name', 'language.code'],
                'separator' => '_',
                'onUpdate' => true,
                'unique' => false,
            ]
        ];
    }

    public static function boot()
    {

        parent::boot();
    }

    public function rootMenuItems()
    {
        return $this->hasMany(MenuItem::class)
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
            'language_id' => $this->language_id,
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

    
    public function language()
    {
        return $this->belongsTo(\App\Language::class, 'language_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

}
