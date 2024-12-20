<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Objects;
use Tests\TestCase;

class ObjectTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/api/object/get_all_records');

        $response->assertStatus(200);
    }

    public function test_get_invalid_key()
    {
        $response = $this->getJson('/api/object/invalidkey');
        $response->assertStatus(404);
    }

    public function test_get_value_with_invalid_timestamp()
    {
        Objects::create([
            'key' => 'test_key',
            'value' => json_encode('test value'),
            'timestamp' => time()
        ]);

        $response = $this->getJson('/api/object/test_key?timestamp=invalid');
        $response->assertStatus(404);
    }

    public function test_store_invalid_object()
    {
        $response = $this->post('/api/object', ['invalid object']);
        $response->assertStatus(400);
    }

    public function test_store_empty_data()
    {
        $response = $this->postJson('/api/object', []);
        $response->assertStatus(400)
            ->assertJson(['message' => 'Invalid JSON format']);
    }
}
