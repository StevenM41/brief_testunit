<?php

// tests/Feature/ProductControllerTest.php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsViewWithProducts(): void
    {
        $products = Product::factory()->count(2)->create();

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products', function ($viewProducts) use ($products) {
            return $viewProducts->count() === $products->count();
        });
    }

    public function testShowReturnsViewWithProduct(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('product', function ($viewProduct) use ($product) {
            return $viewProduct->id === $product->id;
        });
    }

    public function testStoreValidatesAndStoresProduct(): void
    {
        $data = [
            'name' => 'Nouveau produit',
            'description' => 'Description du nouveau produit',
            'price' => 50,
            'stock' => 10,
        ];

        $response = $this->post(route('products.store'), $data);

        $response->assertStatus(302);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', $data);
        $response->assertSessionHas('success', 'Produit ajouté avec succès !');
    }

    public function testStoreValidationErrors(): void
    {
        $response = $this->post(route('products.store'), [
            'name' => '',
            'description' => 'Desc',
            'price' => 'pas-numerique',
            'stock' => 'pas-entier',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'price', 'stock']);
    }

    public function testUpdateProduct(): void
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Produit mis à jour',
            'description' => 'Description mise à jour',
            'price' => 100,
            'stock' => 5,
        ];

        $response = $this->put(route('products.update', $product), $data);

        $response->assertStatus(302);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $data));
        $response->assertSessionHas('success', 'Produit mis à jour avec succès !');
    }

    public function testDestroyProduct(): void
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertStatus(302);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $response->assertSessionHas('success', 'Produit supprimé avec succès !');
    }
}
