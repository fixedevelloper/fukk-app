<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Store extends Model
{


    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'country',
        'state',
        'city',
        'vendor_id',
        'logo',
        'cover_image',
        'description',
        'content',
        'status',
        'company',
        'zip_code',
        'certificate_file',
        'government_id_file',
        'tax_id',
    ];

    protected $casts = [

    ];

    protected static function booted(): void
    {
        static::deleted(function (Store $store) {
            $store->products()->each(fn (Product $product) => $product->delete());
            $store->discounts()->delete();
            $store->orders()->update(['store_id' => null]);

            $folder = Storage::path($store->upload_folder);
            if (File::isDirectory($folder) && Str::endsWith($store->upload_folder, '/' . ($store->slug ?: $store->id))) {
                File::deleteDirectory($folder);
            }
        });

        static::updating(function (Store $store) {
            if ($store->getOriginal('status') != $store->status) {
                $status = $store->status;

                $store->products()->update(['status' => $status]);
            }
        });
    }

    public function logo()
    {
        return $this->belongsTo(Image::class)->withDefault();
    }
    public function cover_image()
    {
        return $this->belongsTo(Image::class)->withDefault();
    }
    public function vendor()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->where('is_finished', 1);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class, 'store_id');
    }



    public function reviews()
    {
        return $this
            ->hasMany(Product::class)
            ->join('ec_reviews', 'ec_products.id', '=', 'ec_reviews.product_id');
    }

    protected function uploadFolder()
    {
        return Attribute::make(
            get: function () {
                $folder = $this->id ? 'stores/' . ($this->slug ?: $this->id) : 'stores';

                return apply_filters('marketplace_store_upload_folder', $folder, $this);
            }
        );
    }

    public static function handleCommissionEachCategory(array $data): array
    {
        $commissions = [];
        CategoryCommission::query()->truncate();
        foreach ($data as $datum) {
            if (! $datum['categories']) {
                continue;
            }

            $categories = json_decode($datum['categories'], true);

            if (! is_array($categories) || ! count($categories)) {
                continue;
            }

            foreach ($categories as $category) {
                $commission = CategoryCommission::query()->firstOrNew([
                    'product_category_id' => $category['id'],
                ]);

                if (! $commission) {
                    continue;
                }

                $commission->commission_percentage = $datum['commission_fee'];
                $commission->save();
                $commissions[] = $commission;
            }
        }

        return $commissions;
    }

    public static function getCommissionEachCategory(): array
    {
        $commissions = CategoryCommission::query()->with(['category'])->get();
        $data = [];
        foreach ($commissions as $commission) {
            if (! $commission->category) {
                continue;
            }

            $data[$commission->commission_percentage]['commission_fee'] = $commission->commission_percentage;
            $data[$commission->commission_percentage]['categories'][] = [
                'id' => $commission->product_category_id,
                'value' => $commission->category->name,
            ];
        }

        return $data;
    }

}
