<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = collect([
            'Novidade',
            'Promoção',
            'Mais Vendido',
            'Premium',
            'Sustentável',
            'Importado',
            'Nacional',
            'Entrega Rápida',
            'Garantia Estendida',
            'Uso Profissional',
            'Compacto',
            'Sem Fios',
        ])->map(fn (string $name): Tag => Tag::factory()->create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]));

        Category::query()->each(function (Category $category) use ($tags): void {
            $category->tags()->attach($tags->random(2)->pluck('id')->all());
        });
    }
}
