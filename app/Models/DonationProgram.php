<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationProgram extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'poster_path',
        'target_amount',
        'short_description',
        'content',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_amount' => 'decimal:2',
    ];

    /**
     * Mendefinisikan relasi ke model Donation.
     * Satu program donasi memiliki banyak donasi.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Relasi ke update perkembangan program.
     * Satu program bisa memiliki banyak update.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ProgramUpdate::class);
    }

    /**
     * Relasi ke pencairan dana program.
     * Satu program bisa memiliki banyak pencairan dana.
     */
    public function disbursements(): HasMany
    {
        return $this->hasMany(FundDisbursement::class);
    }
    /**
     * Accessor untuk mendapatkan total donasi yang terkumpul.
     * Penggunaan: $program->collected_amount
     */
    public function getCollectedAmountAttribute(): float
    {
        // Hanya menjumlahkan donasi yang statusnya sudah 'paid'
        return $this->donations()->where('status', 'paid')->sum('amount');
    }

    /**
     * Accessor untuk mendapatkan persentase progres donasi.
     * Penggunaan: $program->progress_percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        $percentage = ($this->getCollectedAmountAttribute() / $this->target_amount) * 100;

        // Bisa dibatasi maksimal 100% jika diinginkan
        return min($percentage, 100);
    }

    /**
     * Mengubah kunci rute default dari 'id' menjadi 'slug'.
     * Ini memungkinkan Route Model Binding menggunakan slug.
     * Contoh: Route::get('/programs/{program}', ...);
     * URL akan menjadi /programs/bantu-banjir, bukan /programs/1
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
