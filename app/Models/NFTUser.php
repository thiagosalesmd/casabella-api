<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UUID;

class NFTUser extends Model
{
    use HasFactory, UUID;

    protected $table = 'nft_users';

    protected $fillable = [
        'sender_id',
        'nft_id',
        'recipient_id',
        'status'
    ];
}
