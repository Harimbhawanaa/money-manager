<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        Transaction::factory()->count(14)->income()->create();
        Transaction::factory()->count(90)->expense()->create();

        $budgets = [
            'Shopping' => 400000,
            'Hiburan' => 300000,
            'Makanan' => 800000,
            'Laundry' => 150000,
            'Listrik' => 200000,
        ];

        foreach ($budgets as $name => $amount) {
            $category = Category::where('name', $name)->first();

            if ($category) {
                Budget::updateOrCreate(
                    ['category_id' => $category->id, 'month' => now()->month, 'year' => now()->year],
                    ['amount' => $amount]
                );
            }
        }
    }
}
