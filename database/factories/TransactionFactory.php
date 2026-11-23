<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return $this->expenseData();
    }

    public function expense(): static
    {
        return $this->state(fn () => $this->expenseData());
    }

    public function income(): static
    {
        return $this->state(function () {
            $category = Category::where('type', 'income')->inRandomOrder()->first()
                ?? Category::factory()->income()->create();

            return [
                'category_id' => $category->id,
                'type' => 'income',
                'amount' => fake()->numberBetween(10, 60) * 50000,
                'description' => 'Pemasukan: ' . $category->name,
                'transaction_date' => fake()->dateTimeBetween('-11 months', 'now'),
            ];
        });
    }

    private function expenseData(): array
    {
        $category = Category::where('type', 'expense')->inRandomOrder()->first()
            ?? Category::factory()->expense()->create();

        return [
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => fake()->numberBetween(2, 120) * 5000,
            'description' => 'Pengeluaran: ' . $category->name,
            'transaction_date' => fake()->dateTimeBetween('-11 months', 'now'),
        ];
    }
}
