<?php

namespace App\Models;

use App\Models\Turf;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    //
    protected $fillable = [
        'price','currency'
    ];
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
