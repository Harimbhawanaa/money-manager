<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Tabungan', 'expense', '#10b981'],
            ['Shopping', 'expense', '#ec4899'],
            ['Hutang', 'expense', '#ef4444'],
            ['Minjemin', 'expense', '#f97316'],
            ['Listrik', 'expense', '#eab308'],
            ['Hiburan', 'expense', '#8b5cf6'],
            ['Kuliah', 'expense', '#3b82f6'],
            ['Laundry', 'expense', '#06b6d4'],
            ['Makanan', 'expense', '#84cc16'],
            ['Lainnya (Pengeluaran)', 'expense', '#94a3b8'],
            ['Gaji / Uang Saku', 'income', '#22c55e'],
            ['Freelance', 'income', '#14b8a6'],
            ['Kiriman Orang Tua', 'income', '#0ea5e9'],
            ['Lainnya (Pemasukan)', 'income', '#64748b'],
        ];

        foreach ($categories as [$name, $type, $color]) {
            Category::firstOrCreate(['name' => $name], ['type' => $type, 'color' => $color]);
        }
    }
}
