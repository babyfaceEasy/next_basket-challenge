<?php

namespace Tests\Feature;

use App\Jobs\UserCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_store_user_data()
    {
        $userData = [
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'created_at' =>  $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime
        ];

        $response = $this->postJson(route('users.store'), $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'email',
                    'first_name',
                    'last_name',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => 1,
                    'email' => $userData['email'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                ]
            ]);
            
        
        //$this->assertDatabaseHas('users', $userData);
        $this->assertDatabaseCount('users', 1);
    }

    /** @test */
    public function it_requires_email_first_name_and_last_name(): void
    {
        $response = $this->postJson(route('users.store'), []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['email', 'first_name', 'last_name']
            ]);
            //->assertJsonValidationErrors(['email', 'first_name', 'last_name']);
    }

    /** @test */
    public function it_handles_exception_during_user_creation(): void
    {
        // Simulate a duplicate entry to trigger the unique constraint exception
        $duplicateUserData = [
            'email' => 'test@example.com',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
        ];

        User::create($duplicateUserData);
        $response = $this->postJson(route('users.store'), $duplicateUserData);

        $response->assertStatus(500)
            ->assertJsonStructure([
                'success',
                'message',
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Something went wrong, please try again later.'
            ]);
    }

    /** @test */
    public function it_logs_error_during_creation(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function($message){
                return str_contains($message, 'File: ');
        });

        // Simulate a duplicate entry to trigger the unique constraint exception
        $duplicateUserData = [
            'email' => 'test@example.com',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
        ];

        // Disable Laravel exception handling so we can capture the log entry
        $this->withoutExceptionHandling();

        User::create($duplicateUserData);
        $response = $this->postJson(route('users.store'), $duplicateUserData);

        $response->assertStatus(500);
    }

    /** @test */
    public function it_dispatches_user_created_job_after_user_creation()
    {
        Bus::fake();

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        $response = $this->postJson(route('users.store'), $userData);

        $response->assertStatus(200);

        Bus::assertDispatched(UserCreated::class, function ($job) use ($userData) {
            $data = $job->getData();
            return $data['first_name'] === $userData['first_name']
                && $data['last_name']  === $userData['last_name']
                && $data['email']  === $userData['email'];
        });

    }
}
