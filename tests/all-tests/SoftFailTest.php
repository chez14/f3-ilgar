<?php

use PHPUnit\Framework\TestCase;

/**
 * @testdox Soft Fail Test: failure because of false code from the do_migrate method.
 */
class SoftFailTest extends TestCase
{
    protected $f3;

    protected function setUp(): void
    {
        $this->f3 = \F3::instance();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/packages-test-3/");
        $this->f3->set('ILGAR.show_log', false);
        $this->f3->set('QUIET', true);
        \Chez14\Ilgar\Boot::now();
    }

    /**
     * @testdox Able to handle soft fail
     */
    public function testFirstStage()
    {
        $this->f3->set("ILGAR.no_exception", true);
        \Chez14\Ilgar\Internal::instance()->resetVersion();
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \Chez14\Ilgar\Internal::instance()->getStats();
        $this->assertSame(1, $stats['version']);
        $this->assertSame(1, $stats['success']);
        $this->assertNotNull($stats['last_exception']);
    }
}
