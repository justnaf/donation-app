<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundDisbursement extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donation_program_id',
        'amount',
        'description',
        'disbursed_at',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'disbursed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Mendefinisikan relasi ke model DonationProgram.
     * Sebuah pencairan dana dimiliki oleh satu program donasi.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(DonationProgram::class, 'donation_program_id');
    }
}
