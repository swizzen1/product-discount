<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductGroup extends Model
{
    protected $primaryKey = 'group_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['user_id', 'discount'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(ProductGroupItem::class, 'group_id', 'group_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_group_items', 'group_id', 'product_id', 'group_id', 'product_id');
    }
}
