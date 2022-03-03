<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommanderTest extends TestCase
{
    use InteractsWithCommanders,
        DatabaseTransactions;

    public Builder|Model $commander;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->commander = $this->createCommander(
            'Test Commander', // name
            1000, // fund
            1, // 1% risk,
            123456 // 3Commas bot ID
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_commander_instance()
    {
        $commander = $this->commander;

        $this->assertInstanceOf(Commander::class, $commander);
        $this->assertEquals('Test Commander', $commander->name);
        $this->assertEquals(1000, $commander->fund);
        $this->assertEquals(1, $commander->risk);
        $this->assertEquals(123456, $commander->bot_id);
        $this->assertEquals(Commander::STATUS_CHILL, $commander->status);
    }
}
