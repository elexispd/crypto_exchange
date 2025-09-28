<?php

namespace Database\Factories;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\SecretPhrase;


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
     use SecretPhrase;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'solana_address' => Str::uuid(),
            'secret_phrase' => $this->createSecretPhrase(),
            'btc_address' => $this->faker->uuid(),
            'xrp_address' => $this->faker->uuid(),
            'eth_address' => $this->faker->uuid(),
            'created_by' => $this->faker->uuid(),
        ];
    }
}
