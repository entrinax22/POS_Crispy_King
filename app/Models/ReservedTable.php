<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedTable extends Model
{
    protected $fillable = [
        'table_id',
        'reserved_by',
        'name',
        'reservation_time',
        'status',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
    ];

    protected $table = 'reserved_tables';
    protected $primaryKey = 'id';

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
}
