<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'api_service_id',
        'token_type_id',
        'token_value',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        ];

    public function company(): BelongsTo{
        return $this->belongsTo(Company::class);
    }

    public function apiService(): BelongsTo{
        return $this->belongsTo(ApiService::class);
    }

    public function tokenType(): BelongsTo{
        return $this->belongsTo(TokenType::class);
    }

    public function data(): HasMany{
        return $this->hasMany(Data::class);
    }
}
