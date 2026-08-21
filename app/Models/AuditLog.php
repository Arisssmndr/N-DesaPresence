<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $table = 'audit_logs';
    protected $fillable = [
        'user_id', 'user_name', 'role', 'aktivitas', 'modul',
        'data_sebelum', 'data_sesudah', 'ip_address', 'user_agent', 'created_at'
    ];

    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
            if (empty($model->ip_address) && request()) {
                $model->ip_address = request()->ip();
            }
            if (empty($model->user_agent) && request()) {
                $model->user_agent = request()->userAgent();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
