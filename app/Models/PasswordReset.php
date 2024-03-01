<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PasswordReset
 */
class PasswordReset extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'password_resets';

    protected $fillable = [
        'email',
        'token',

    ];

    /**
     * Method boot
     *
     * @return View|\Illuminate\Contracts\View\Factory
     */
    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($model): void {
                $model->created_at = $model->freshTimestamp();
            }
        );
    }
}
