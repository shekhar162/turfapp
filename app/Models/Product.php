<?php

namespace App\Models;

use App\Models\Order;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name','mobimobileNumber','closedDays','closedHrs','suitableFor','otherFacility','status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'mobileNumber', 'mobileNumber');
    }
    
    public function price(){
        return $this->hasOne(ProductPrice::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
