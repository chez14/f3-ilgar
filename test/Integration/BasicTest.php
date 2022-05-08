<?php

namespace CHEZ14\Ilgar\Test\Integration;

use CHEZ14\Ilgar\Test\Utils\DBSetup;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Basic Test
 */
class BasicTest extends TestCase
{
    protected $f3;

    /**
     * Runs
     *
     * @return void
     */
    protected function setUp(): void
    {
        try {
            DBSetup::setup();
        } catch (\InvalidArgumentException $e) {
            $this->markTestSkipped("Database is not set");
        }

        $this->f3 = \F3::instance();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/../.test-files/packages-test-1/");
        $this->f3->set('ILGAR.show_log', false);
        $this->f3->set('QUIET', true);
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @test
     * @testdox Able to do simple migration
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
