<?php

namespace CHEZ14\Ilgar\Test\Unit;

use CHEZ14\Ilgar\Migration;
use CHEZ14\Ilgar\Runner;
use Error;
use F3;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

/**
 * MigrationRunnerTest
 *
 * Test for migration pack to call the `up` method, skip if the pack not enabled
 * and forward the exception when we got one.
 *
 * @group Migration
 */
class MigrationRunnerTest extends TestCase
{
    /**
     * Sets up the testing suite for this particular test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUpBeforeClass();


        // Reset current instance to its default state.
        $this->f3 = F3::instance();
        $this->f3->set('ILGAR.path', dirname(__DIR__) . "/../.test-files/virtualfolder-a/");
        $this->f3->set('ILGAR.' . Runner::CONFIG_WEBLOG, false);

        // Trigger the setup sequence
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @test
     *
     * Migration Class Expected to invoke `up()` when triggered by `run()`.
     *
     * @covers CHEZ14\Ilgar\Migration
     *
     * @return void
     * */
    public function migrationClassProperlyCallsUp()
    {
        $stub = $this->getMockForAbstractClass(Migration::class, [Runner::instance()]);

        $stub->expects($this->once())
            ->method('up');

        $this->assertTrue($stub->run());
    }

    /**
     * @test
     *
     * Migration Class Expected to invoke `up()` when triggered by `run()`.
     *
     * @covers CHEZ14\Ilgar\Migration
     *
     * @return void
     * */
    public function migrationClassProperlyNotCallsUpOnDisable()
    {
        $stub = $this->getMockForAbstractClass(Migration::class, [Runner::instance()]);

        $reflection = new ReflectionClass(Migration::class);
        $reflection_property = $reflection->getProperty('enabled');
        $reflection_property->setAccessible(true);

        // disables the Migration class property.
        $reflection_property->setValue($stub, false);

        $stub->expects($this->never())
            ->method('up');

        $this->assertFalse($stub->run());
    }

    /**
     * @test
     *
     * Migration Class Expected to invoke `up()` when triggered by `run()`.
     *
     * @covers CHEZ14\Ilgar\Migration
     *
     * @return void
     * */
    public function migrationClassProperlyHandleException()
    {
        $stub = $this->getMockForAbstractClass(Migration::class, [Runner::instance()]);

        $stub->expects($this->once())
            ->method('up')
            ->willThrowException(new Error("Just a generic error"));

        $this->expectException(RuntimeException::class);
        $stub->run();
    }
}
