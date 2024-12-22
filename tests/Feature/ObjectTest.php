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
    public function test_get_all_records(): void
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
        $object = Objects::create([
            'key' => 'test_key',
            'value' => 'test value',
            'timestamp' => time()
        ]);

        $response = $this->getJson('/api/object/test_key?timestamp=invalid');
        $response->assertStatus(400);

        $object->delete();
    }

    public function test_store_to_invalid_path()
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

    public function test_store_invalid_key()
    {
        $response = $this->postJson('/api/object', [str_repeat('a', 256) => 'sample value']);
        $response->assertStatus(400);
    }

    public function test_store_valid_key_string_value()
    {
        $response = $this->postJson('/api/object', ['mykey-test-string' => 'sample value']);
        $response->assertStatus(201);
    }

    public function test_get_valid_key()
    {
        $response = $this->get('/api/object/mykey-test-string');
        $response->assertStatus(200);
    }

    public function test_get_valid_key_with_timestamp()
    {
        $response = $this->get('/api/object/mykey-test-string?timestamp='.time());
        $response->assertStatus(200);
        $object = Objects::where('key', 'mykey-test-string')->first();
        $object->delete();
    }

    public function test_store_valid_key_with_binary_value()
    {
        $binaryData = file_get_contents(base_path('tests/fixtures/phpicon.png'));
        $response = $this->postJson('/api/object', ['mykey-test-binarydata.png' => base64_encode($binaryData)]);
        $response->assertStatus(201);
    }

    public function test_get_valid_key_with_binary_value()
    {
        $response = $this->get('/api/object/mykey-test-binarydata.png');
        $response->assertStatus(200);
        $object = Objects::where(['key' => 'mykey-test-binarydata.png'])->first();
        $object->delete();
    }

    public function test_store_valid_key_with_nonbinary_value()
    {
        $nonbinaryData = "This is a sample binary data.";
        $response = $this->postJson('/api/object', ['mykey-test-nonbinary' => base64_encode($nonbinaryData)]);
        $response->assertStatus(201);
    }

    public function test_get_valid_key_with_nonbinary_value()
    {
        $response = $this->get('/api/object/mykey-test-nonbinary');
        $response->assertStatus(200);
        $object = Objects::where(['key' => 'mykey-test-nonbinary'])->first();
        $object->delete();

    }

}
