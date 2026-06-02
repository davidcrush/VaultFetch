<?php

namespace Database\Factories;

use App\DownloadStatus;
use App\Models\Download;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Download>
 */
class DownloadFactory extends Factory
{
    protected $model = Download::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'url' => 'https://www.youtube.com/watch?v='.$this->faker->regexify('[A-Za-z0-9_-]{11}'),
            'external_id' => $this->faker->regexify('[A-Za-z0-9_-]{11}'),
            'title' => $this->faker->sentence(4),
            'duration_seconds' => $this->faker->numberBetween(60, 7200),
            'status' => DownloadStatus::Completed,
            'file_path' => null,
            'error_message' => null,
            'fetched_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DownloadStatus::Pending,
            'fetched_at' => null,
        ]);
    }
}
