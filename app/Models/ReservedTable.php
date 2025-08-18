<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedTable extends Model
{
    protected $fillable = [
        'table_id',
        'reserved_by',
        'name',
        'contact_number',
        'number_guest',
        'reservation_time',
        'status',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
    ];

    protected $table = 'reserved_tables';
    protected $primaryKey = 'reserved_table_id';

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
}
