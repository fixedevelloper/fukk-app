<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class StoreOrder extends Model
{

    protected $fillable=[
      'order_id'  ,
        'store_id' ,
        'total_amount' ,
        'status'   ,
        'payment_status',
    ];
    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function orderProducts() {
        return $this->hasMany(OrderProduct::class);
    }
    public function store() {
        return $this->belongsTo(Store::class);
    }
}
