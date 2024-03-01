<?php

namespace Tests\Unit;

use App\Jobs\UserCreated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserCreatedJobTest extends TestCase
{

    use WithFaker;

    /** @test */
    public function it_logs_data_to_a_file()
    {
        Log::shouldReceive('info')
            ->once()
            ->with([
                'email' => 'michelleAl@gmail.com',
                'first_name' => 'michelle',
                'last_name' => 'Alozie',
                'updated_at' => '2024-02-29T13:49:49.000000Z',
                'created_at' => '2024-02-29T13:49:49.000000Z',
                'id' => 3,
            ]);

        $userData = [
            'email' => 'michelleAl@gmail.com',
            'first_name' => 'michelle',
            'last_name' => 'Alozie',
            'updated_at' => '2024-02-29T13:49:49.000000Z',
            'created_at' => '2024-02-29T13:49:49.000000Z',
            'id' => 3,
        ];

        $result = (new UserCreated($userData))->handle();

        $this->assertNull($result);
    }

    
}
