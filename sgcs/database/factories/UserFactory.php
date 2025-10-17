<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'correo' => fake()->unique()->safeEmail(),
            'nombre_completo' => fake()->name(),
            'correo_verificado_en' => now(),
            'contrasena_hash' => static::$password ??= Hash::make('password'),
            'activo' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indica que la dirección de correo del modelo debe estar sin verificar.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'correo_verificado_en' => null,
        ]);
    }

    /**
     * Indica que el usuario está inactivo.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
