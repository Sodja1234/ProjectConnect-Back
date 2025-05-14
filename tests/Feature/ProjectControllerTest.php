<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
   
    /** @test */
    public function creates_a_project()
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'Nouveau projet',
           
            'description' => 'Description de test',
            'date_start' => '2025-05-15',
            'date_end' => '2025-06-01',
            'budget' => 1000.00,
            'location' => 'Paris',
        
            'visibility' => 'public',
             'created_by' => $user->id,
            'updated_by' => $user->id
        ];

        $response = $this->postJson('/api/projects', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Nouveau projet']);

        $this->assertDatabaseHas('projects', [
            'title' => 'Nouveau projet',
            'created_by' => $user->id,
        ]);
    }
}