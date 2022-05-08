<?php

namespace CHEZ14\Ilgar\Test\Integration;

use CHEZ14\Ilgar\Test\Utils\DBSetup;
use PHPUnit\Framework\TestCase;

/**
 * @testdox is_migratable
 */
class SkippingTest extends TestCase
{
    protected $f3;

    protected function setUp(): void
    {
        try {
            DBSetup::setup();
        } catch (\InvalidArgumentException $e) {
            $this->markTestSkipped("Database is not set");
        }

        $this->f3 = \F3::instance();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/.test-files/packages-test-1/");
        $this->f3->set('ILGAR.show_log', false);
        $this->f3->set('QUIET', true);
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @testdox Able to skip a migration package when is_migratable is false.
     * @covers CHEZ14\Ilgar\Migration
     */
    public function testFirstStage()
    {
        \CHEZ14\Ilgar\Runner::instance()->resetVersion();
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \CHEZ14\Ilgar\Runner::instance()->getStats();
        $this->assertSame(3, $stats['version']);
        $this->assertSame(2, $stats['success']);
        $this->assertNull($stats['last_exception']);
    }

    /**
     * @testdox Able to skip a migration package even if it's haven't executed.
     * @depends testFirstStage
     * @covers CHEZ14\Ilgar\Migration
     */
    public function testSecondStage()
    {
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \CHEZ14\Ilgar\Runner::instance()->getStats();
        $this->assertSame(3, $stats['version']);
        $this->assertSame(0, $stats['success']);
        $this->assertNull($stats['last_exception']);
    }
}
