<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetitionSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'nome',
        'email',
        'tel_whatsapp',
        'estado',
        'cidade',
        'link_facebook',
        'link_instagram',
        'observacao',
        'ip_address',
        'user_agent',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getFormattedWhatsappAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->tel_whatsapp);
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }
        return $this->tel_whatsapp;
    }
}
