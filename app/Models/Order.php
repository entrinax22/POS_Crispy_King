<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'address',
        'phone_number',
        'delivery_time',
        'total_amount',
        'status',
        'order_type',
        'cancelled_at'
    ];

    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderedItem::class, 'order_id');
    }

}
