<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('conversaciones')]
#[Fillable(['user_id', 'titulo'])]
class Conversacion extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('propia', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('user_id', auth()->id());
            }
        });
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class)->orderBy('created_at');
    }
}
