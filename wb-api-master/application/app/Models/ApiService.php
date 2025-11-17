<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class ApiService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function supportedTokenTypes(): BelongsToMany
    {
        return $this->belongsToMany(TokenType::class, 'service_token_types');
    }
}
