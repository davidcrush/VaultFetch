<?php

namespace App\Models;

use App\DownloadStatus;
use Carbon\CarbonInterface;
use Database\Factories\DownloadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    /** @use HasFactory<DownloadFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'url',
        'external_id',
        'title',
        'duration_seconds',
        'status',
        'file_path',
        'error_message',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => DownloadStatus::class,
            'fetched_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  mixed  $value
     * @param  string|null  $field
     */
    public function resolveRouteBinding($value, $field = null): Model
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function formattedDuration(): string
    {
        return $this->humanDuration();
    }

    public function humanDuration(): string
    {
        $seconds = $this->duration_seconds ?? 0;

        if ($seconds <= 0) {
            return '—';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours.' '.($hours === 1 ? 'hour' : 'hours');
        }

        if ($minutes > 0) {
            $parts[] = $minutes.' '.($minutes === 1 ? 'minute' : 'minutes');
        }

        if ($parts === [] && $remainingSeconds > 0) {
            $parts[] = $remainingSeconds.' '.($remainingSeconds === 1 ? 'second' : 'seconds');
        }

        return implode(' ', $parts);
    }

    public function addedAt(): CarbonInterface
    {
        return $this->fetched_at ?? $this->created_at;
    }

    public function formattedAddedAt(): string
    {
        return $this->addedAt()->format('M j, Y g:i A');
    }

    public function expiresAt(): ?CarbonInterface
    {
        if ($this->fetched_at === null) {
            return null;
        }

        return $this->fetched_at->copy()->addDays(config('vaultfetch.retention_days'));
    }

    public function formattedExpiresAt(): string
    {
        $expiresAt = $this->expiresAt();

        if ($expiresAt === null) {
            return '—';
        }

        return $expiresAt->format('M j, Y g:i A');
    }

    public function isExpired(): bool
    {
        if ($this->hasExpiredFile()) {
            return true;
        }

        if ($this->status !== DownloadStatus::Completed) {
            return false;
        }

        $expiresAt = $this->expiresAt();

        return $expiresAt !== null && Carbon::now()->greaterThanOrEqualTo($expiresAt);
    }

    public function canRefetch(): bool
    {
        return $this->isExpired() && $this->url !== '';
    }

    public function isFileAvailable(): bool
    {
        return $this->file_path !== null
            && $this->status === DownloadStatus::Completed
            && Storage::disk('local')->exists($this->file_path);
    }

    public function hasExpiredFile(): bool
    {
        return $this->status === DownloadStatus::Completed
            && $this->file_path === null;
    }
}
