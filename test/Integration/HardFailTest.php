<?php

namespace CHEZ14\Ilgar\Test\Integration;

use CHEZ14\Ilgar\Test\Utils\DBSetup;
use PHPUnit\Framework\TestCase;
use Tests\Integration\BaseTest;

/**
 * @testdox Hard Failure (Failure that caused by exceptions)
 */
class HardFailTest extends BaseTest
{
    /**
     * Undocumented function
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/.test-files/packages-test-4/");
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @test
     * @testdox Able to handle the exception without throwing exception
     *
     * @covers CHEZ14\Ilgar\Migration
     */
    public function testFirstStage()
    {
        $this->f3->set("ILGAR.no_exception", true);
        \CHEZ14\Ilgar\Runner::instance()->resetVersion();
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \CHEZ14\Ilgar\Runner::instance()->getStats();
        $this->assertSame(1, $stats['version']);
        $this->assertSame(1, $stats['success']);
        $this->assertNotNull($stats['last_exception']);
    }
}
