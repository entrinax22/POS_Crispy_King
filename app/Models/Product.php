<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'product_code',
        'product_description',
        'product_price',
        'product_quantity',
        'product_image',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
