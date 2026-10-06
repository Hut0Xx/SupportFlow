<?php
namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
class UserFactory extends Factory
{
    protected $model = User::class;
    public function definition(): array
    {
        return [
            "organization_id" => fn() => \DB::table(
                "organizations",
            )->insertGetId([
                "name" => fake()->company(),
                "slug" => fake()->unique()->slug(),
                "created_at" => now(),
                "updated_at" => now(),
            ]),
            "name" => fake()->name(),
            "email" => fake()->unique()->safeEmail(),
            "email_verified_at" => now(),
            "password" => Hash::make("password-for-tests"),
            "active" => true,
        ];
    }
}
