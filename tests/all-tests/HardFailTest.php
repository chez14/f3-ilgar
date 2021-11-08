<?php

use PHPUnit\Framework\TestCase;

/**
 * @testdox Hard Failure (Failure that caused by exceptions)
 */
class HardFailTest extends TestCase
{
    protected $f3;

    protected function setUp(): void
    {
        $this->f3 = \F3::instance();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/packages-test-4/");
        $this->f3->set('ILGAR.show_log', false);
        $this->f3->set('QUIET', true);
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @testdox Able to handle the exception without throwing exception
     */
    public function testFirstStage()
    {
        $this->f3->set("ILGAR.no_exception", true);
        \CHEZ14\Ilgar\Internal::instance()->resetVersion();
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \CHEZ14\Ilgar\Internal::instance()->getStats();
        $this->assertSame(1, $stats['version']);
        $this->assertSame(1, $stats['success']);
        $this->assertNotNull($stats['last_exception']);
    }
}
