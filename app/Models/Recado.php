<?php

namespace App\Models;

use Database\Factories\RecadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $texto
 * @property bool $destaque
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Recado extends Model
{
    /** @use HasFactory<RecadoFactory> */
    use HasFactory;

    protected $fillable = ['texto'];

    protected $casts = ['destaque' => 'boolean'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
