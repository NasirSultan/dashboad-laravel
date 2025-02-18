<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    use HasFactory;

    // Define the table name if it differs from the pluralized version of the model
    protected $table = 'scheduled_messages';

    // Specify the fields that are mass-assignable
    protected $fillable = ['message', 'send_at'];

    // If you are using timestamps for created_at and updated_at, no need to specify them here as they are default
    public $timestamps = true;
}
