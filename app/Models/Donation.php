<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'donation_program_id',
        'user_id',
        'donator_name',
        'donator_email',
        'amount',
        'fee',
        'message',
        'is_anonymous',
        'status',
        'payment_method',
        'payment_details',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'payment_details' => 'array',
    ];

    /**
     * Mendefinisikan relasi ke model DonationProgram.
     * Sebuah donasi dimiliki oleh satu program donasi.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(DonationProgram::class, 'donation_program_id');
    }

    /**
     * Mendefinisikan relasi ke model User.
     * Sebuah donasi bisa dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Query scope untuk memfilter donasi yang sudah lunas (paid).
     * Penggunaan: Donation::paid()->get();
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    /**
     * Helper method untuk mendapatkan nama donatur yang akan ditampilkan ke publik.
     * Menghormati flag 'is_anonymous'.
     */
    public function getDonatorDisplayName(): string
    {
        return $this->is_anonymous ? 'Hamba Allah' : $this->donator_name;
    }
}
