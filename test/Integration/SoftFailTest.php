<?php

namespace CHEZ14\Ilgar\Test\Integration;

use CHEZ14\Ilgar\Test\Utils\DBSetup;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Soft Fail Test: failure because of false code from the do_migrate method.
 */
class SoftFailTest extends TestCase
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
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/.test-files/packages-test-3/");
        $this->f3->set('ILGAR.show_log', false);
        $this->f3->set('ILGAR.disable_ob', false);
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @test
     * @testdox Able to handle soft fail
     * @covers CHEZ14\Ilgar\Migration
     *
     */
    public function testFirstStage()
    {
        $this->f3->set("ILGAR.no_exception", true);
        \CHEZ14\Ilgar\Runner::instance()->resetVersion();
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \CHEZ14\Ilgar\Runner::instance()->getStats();
        $this->assertSame(1, $stats['version']);
        $this->assertSame(1, $stats['success']);

        // Soft fail should not throw exception now.
        $this->assertNull($stats['last_exception']);
    }
}
