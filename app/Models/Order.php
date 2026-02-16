<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'status',
        'user_id',
        'total_amount',
        'tax_amount',
        'shipping_amount',
        'description',
        'coupon_code',
        'discount_amount',
        'sub_total',
        'is_confirmed',
        'discount_description',
        'is_finished',
        'cancellation_reason',
        'cancellation_reason_description',
        'token',
        'completed_at',
        'proof_file',
    ];

    protected $casts = [
        //'status' => OrderStatusEnum::class,
        //'shipping_method' => ShippingMethodEnum::class,
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        self::deleted(function (Order $order) {
            $order->shipment()->each(fn (Shipment $item) => $item->delete());
            $order->histories()->delete();
            $order->products()->delete();
            $order->address()->delete();
            $order->invoice()->each(fn (Invoice $item) => $item->delete());
        });

       // static::creating(fn (Order $order) => $order->code = static::generateUniqueCode());
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }
    public function storeOrders() {
        return $this->hasMany(StoreOrder::class);
    }

    public function shippingAddress()
    {
        return $this
            ->hasOne(OrderAddress::class, 'order_id')
            ->withDefault();
    }

    public function billingAddress()
    {
        return $this
            ->hasOne(OrderAddress::class, 'order_id')
            ->withDefault();
    }

    public function referral()
    {
        return $this->hasOne(OrderReferral::class, 'order_id')->withDefault();
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id')->with(['product']);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id')->with(['user', 'order']);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class)->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'reference_id')->withDefault();
    }

    public function taxInformation()
    {
        return $this->hasOne(OrderTaxInformation::class, 'order_id');
    }



    public function getIsFreeShippingAttribute(): bool
    {
        return $this->shipping_amount == 0 && $this->discount_amount == 0 && $this->coupon_code;
    }


    public function returnRequest()
    {
        return $this->hasOne(OrderReturn::class, 'order_id')->withDefault();
    }

    public function digitalProducts()
    {
        return $this->products->filter(fn ($item) => $item->isTypeDigital());
    }


}
