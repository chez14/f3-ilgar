<?php
use PHPUnit\Framework\TestCase;

/**
 * @testdox is_migratable 
 */
class SkippingTest extends TestCase
{
    protected $f3;
    
    protected function setUp():void {
        $this->f3 = \F3::instance();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/packages-test-2/");
        $this->f3->set('ILGAR.show_log', false);
        $this->f3->set('QUIET',TRUE);
        \Chez14\Ilgar\Boot::now();
    }

    /**
     * @testdox Able to skip a migration package when is_migratable is false.
     */
    public function testFirstStage() {
        \Chez14\Ilgar\Internal::instance()->reset_version();
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \Chez14\Ilgar\Internal::instance()->get_stats();
        $this->assertSame(3, $stats['version']);
        $this->assertSame(2, $stats['success']);
        $this->assertNull($stats['last_exception']);
    }

    /**
     * @testdox Able to skip a migration package even if it's haven't executed.
     * @depends testFirstStage
     */
    public function testSecondStage() {
        $this->f3->mock('GET /ilgar/migrate');
        $stats = \Chez14\Ilgar\Internal::instance()->get_stats();
        $this->assertSame(3, $stats['version']);
        $this->assertSame(0, $stats['success']);
        $this->assertNull($stats['last_exception']);
    }
}