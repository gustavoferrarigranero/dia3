<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            'Computadores',
            'Smartphones',
            'Áudio',
            'Casa Inteligente',
            'Gaming',
            'Acessórios',
        ])->each(fn (string $name): Category => Category::factory()->create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]));
    }
}
