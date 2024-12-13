<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Specify the table name (optional if the table name is `products`)
    protected $table = 'products';

    // Specify the primary key (optional if it is `id`)
    protected $primaryKey = 'id';

    // Allow mass assignment for specific columns
    protected $fillable = ['name', 'file_path', 'price'];
}
