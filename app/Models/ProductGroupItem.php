<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGroupItem extends Model
{
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['group_id', 'product_id'];

    public function group()
    {
        return $this->belongsTo(UserProductGroup::class, 'group_id', 'group_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
