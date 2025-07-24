<?php

// tests/Unit/ProductTest.php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class productTest extends TestCase
{
    use RefreshDatabase;

    public function testCreation(): void
    {
        $post = Product::create([
            'name' => 'Un produit',
            'description' => 'Description du produit',
            'price' => 520,
            'stock' => 100,
        ]);

        $createdPost = Product::find($post->id);

        $this->assertNotNull($createdPost);
        $this->assertEquals('Un produit', $createdPost->name);
        $this->assertEquals('Description du produit', $createdPost->description);
        $this->assertEquals(520, $createdPost->price);
        $this->assertEquals(100, $createdPost->stock);
    }

    public function testReading(): void
    {
        $products = Product::factory()->count(3)->create();

        $all = Product::all();
        $this->assertCount(3, $all);
    }

    public function testEdition(): void
    {
        $product = Product::create([
            'name' => 'Produit initial',
            'description' => 'Description initiale',
            'price' => 100,
            'stock' => 50,
        ]);

        $product->update([
            'name' => 'Produit mis à jour',
            'description' => 'Nouvelle description',
            'price' => 150,
            'stock' => 20,
        ]);

        $updated = Product::find($product->id);

        $this->assertNotNull($updated);
        $this->assertEquals('Produit mis à jour', $updated->name);
        $this->assertEquals('Nouvelle description', $updated->description);
        $this->assertEquals(150, $updated->price);
        $this->assertEquals(20, $updated->stock);
    }

    public function testDeletion(): void
    {
        $product = Product::create([
            'name' => 'Produit à supprimer',
            'description' => 'Description temporaire',
            'price' => 75,
            'stock' => 5,
        ]);

        $this->assertNotNull($product);
        $product->delete();
        $this->assertNull(Product::find($product->id));
    }

    public function testRejetPrixNegatif(): void
    {
        $this->expectException(QueryException::class);

        $post = Product::create([
            'name'        => 'Produit invalide',
            'description' => 'Prix négatif interdit',
            'price'       => -64,
            'stock'       => 10,
        ]);

        $createdPost = Product::find($post->id);

        $this->assertNotNull($createdPost);
        $this->assertEquals('Produit invalide', $createdPost->name);
        $this->assertEquals('Prix négatif interdit', $createdPost->description);
        $this->assertEquals(-64, $createdPost->price);
        $this->assertEquals(10, $createdPost->stock);
    }
}
