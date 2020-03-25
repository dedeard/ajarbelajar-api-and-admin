<?php

namespace App\Helpers;

use App\Model\Category as ModelCategory;
use Illuminate\Support\Facades\Cache;

class Category
{
  public static function all()
  {
    return Cache::rememberForever('category', function () {
      return ModelCategory::all();
    });
  }
}