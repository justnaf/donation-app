<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DonationCategory;

class DonationProgram extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donation_category_id',
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
     * Atribut virtual yang akan ditambahkan saat model diubah menjadi array/JSON.
     * @var array
     */
    protected $appends = [
        'poster_url',
        'collected_amount',
        'progress_percentage',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(DonationCategory::class, 'donation_category_id');
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
        // Gunakan hasil dari withSum jika ada untuk efisiensi, jika tidak baru hitung manual.
        return $this->donations_sum_amount ?? $this->donations()->where('status', 'paid')->sum('amount');
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
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Accessor untuk atribut 'poster_url'.
     *
     * @return string
     */
    public function getPosterUrlAttribute(): string
    {
        if (empty($this->poster_path)) {
            return 'https://via.placeholder.com/500x300.png?text=No+Image';
        }

        if (Str::startsWith($this->poster_path, 'http')) {
            return $this->poster_path;
        }

        // Gunakan Storage::disk('public') agar lebih eksplisit dan aman
        return Storage::url($this->poster_path);
    }
}
