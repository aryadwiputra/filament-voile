<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Casts\MoneyCast;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'slug',
        'price',
        'thumbnail',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    protected $casts = [
        'price' => MoneyCast::class,
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function photos(){
        return $this->hasMany(ProductPhoto::class);
    }
}
