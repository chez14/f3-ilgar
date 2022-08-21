<?php

namespace CHEZ14\Ilgar\Test\Integration;

use CHEZ14\Ilgar\Test\Utils\DBSetup;
use Tests\Integration\BaseTest;

/**
 * @testdox Basic Test
 */
class BasicTest extends BaseTest
{
    /**
     * Runs
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/.test-files/packages-test-1/");
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @test
     * @testdox Able to do simple migration
     *
     * @covers CHEZ14\Ilgar\Migration
     */
    public function testFirstStage()
    {
        \CHEZ14\Ilgar\Runner::instance()->resetVersion();
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \CHEZ14\Ilgar\Runner::instance()->getStats();
        $this->assertSame(1, $stats['version']);
        $this->assertSame(1, $stats['success']);
        $this->assertNull($stats['last_exception']);
    }

    /**
     * @test
     * @testdox Able to prevent reinvoking the same migration package
     * @depends testFirstStage
     * @covers CHEZ14\Ilgar\Migration
     */
    public function testSecondStage()
    {
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \CHEZ14\Ilgar\Runner::instance()->getStats();
        $this->assertSame(1, $stats['version']);
        $this->assertSame(0, $stats['success']);
        $this->assertNull($stats['last_exception']);
    }
}
