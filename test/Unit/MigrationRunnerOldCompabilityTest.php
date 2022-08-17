<?php

namespace CHEZ14\Ilgar\Test\Unit;

use CHEZ14\Ilgar\MigrationPacket;
use CHEZ14\Ilgar\Runner;
use Error;
use F3;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

/**
 * MigrationRunnerOldCompabilityTest
 * @group Migration
 */
class MigrationRunnerOldCompabilityTest extends TestCase
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
        $this->f3->set('ILGAR.disable_ob', false);

        // Trigger the setup sequence
        \CHEZ14\Ilgar\Boot::now();
    }

    /**
     * @test
     *
     * @testdox Make sure old migration with old API design runs as expected without
     * breaking apart.
     *
     * @covers CHEZ14\Ilgar\MigrationPack::run(),
     * CHEZ14\Ilgar\MigrationPack::up(),
     * CHEZ14\Ilgar\MigrationPack::pre_migrate(),
     * CHEZ14\Ilgar\MigrationPack::on_migrate()
     * CHEZ14\Ilgar\MigrationPack::post_migrate()
     * @return void
     */
    public function methodTriggeredOnNormalOperations()
    {
        $stub = $this->getMockForAbstractClass(
            MigrationPacket::class,
            [Runner::instance()],
            '',
            true,
            true,
            true,
            ['pre_migrate', 'post_migrate']
        );
        $stub->expects($this->once())
            ->method('pre_migrate');
        $stub->expects($this->once())
            ->method('on_migrate');
        $stub->expects($this->once())
            ->method('post_migrate');

        $this->assertTrue($stub->run());
    }

    /**
     * @test
     * @testdox Don't run migration when the migration is set to not migratable.
     *
     * @covers CHEZ14\Ilgar\MigrationPack::run(),
     * CHEZ14\Ilgar\MigrationPack::up(),
     * CHEZ14\Ilgar\MigrationPack::is_migratable(),
     * CHEZ14\Ilgar\MigrationPack::pre_migrate(),
     * CHEZ14\Ilgar\MigrationPack::on_migrate()
     * CHEZ14\Ilgar\MigrationPack::post_migrate()
     *
     * @return void
     */
    public function methodWillNotBeCalledOnIsNotMigratable()
    {
        $stub = $this->getMockForAbstractClass(
            MigrationPacket::class,
            [Runner::instance()],
            '',
            true,
            true,
            true,
            ['pre_migrate', 'post_migrate', 'is_migratable']
        );


        // Set is_migratable to false, but still set the enabled as true so it
        // will not be skipped.
        $stub->expects($this->once())
            ->method('is_migratable')
            ->will($this->returnValue(false));

        $stub->expects($this->never())
            ->method('pre_migrate');
        $stub->expects($this->never())
            ->method('on_migrate');
        $stub->expects($this->never())
            ->method('post_migrate');

        $this->assertFalse($stub->run());
    }

    /**
     * @test
     * @testdox Don't run migration when migration is set to not enabled
     * (skipped).
     *
     * @covers CHEZ14\Ilgar\MigrationPack::run(),
     * CHEZ14\Ilgar\MigrationPack::up(),
     * CHEZ14\Ilgar\MigrationPack::is_migratable(),
     * CHEZ14\Ilgar\MigrationPack::pre_migrate(),
     * CHEZ14\Ilgar\MigrationPack::on_migrate()
     * CHEZ14\Ilgar\MigrationPack::post_migrate()
     *
     * @return void
     */
    public function methodWillNotBeCalledWhenEnabledIsFalse()
    {
        $stub = $this->getMockForAbstractClass(
            MigrationPacket::class,
            [Runner::instance()],
            '',
            true,
            true,
            true,
            ['pre_migrate', 'post_migrate', 'is_migratable']
        );


        $reflection = new ReflectionClass(MigrationPacket::class);
        $reflection_property = $reflection->getProperty('enabled');
        $reflection_property->setAccessible(true);

        // disables the Migration class property.
        $reflection_property->setValue($stub, false);

        $stub->expects($this->never())
            ->method('pre_migrate');
        $stub->expects($this->never())
            ->method('on_migrate');
        $stub->expects($this->never())
            ->method('post_migrate');

        $this->assertFalse($stub->run());
    }

    /**
     * @test
     * @testdox Migration should not run when the migration set to not enabled
     * and not migratable.
     *
     * @covers CHEZ14\Ilgar\MigrationPack::run(),
     * CHEZ14\Ilgar\MigrationPack::up(),
     * CHEZ14\Ilgar\MigrationPack::is_migratable(),
     * CHEZ14\Ilgar\MigrationPack::pre_migrate(),
     * CHEZ14\Ilgar\MigrationPack::on_migrate()
     * CHEZ14\Ilgar\MigrationPack::post_migrate()
     * @return void
     */
    public function methodWillNotBeCalledWhenEnabledIsFalseAndNotMigratable()
    {
        $stub = $this->getMockForAbstractClass(
            MigrationPacket::class,
            [Runner::instance()],
            '',
            true,
            true,
            true,
            ['pre_migrate', 'post_migrate', 'is_migratable']
        );


        $reflection = new ReflectionClass(MigrationPacket::class);
        $reflection_property = $reflection->getProperty('enabled');
        $reflection_property->setAccessible(true);

        // disables the Migration class property.
        $reflection_property->setValue($stub, false);

        // Set is_migratable to false, but still set the enabled as true so it
        // will not be skipped.
        $stub->expects($this->never())
            ->method('is_migratable')
            ->will($this->returnValue(false));

        $stub->expects($this->never())
            ->method('pre_migrate');
        $stub->expects($this->never())
            ->method('on_migrate');
        $stub->expects($this->never())
            ->method('post_migrate');

        $this->assertFalse($stub->run());
    }

    /**
     * @test
     *
     * @testdox Migration should handle exception as expected.
     *
     * @covers CHEZ14\Ilgar\MigrationPack::run(),
     * CHEZ14\Ilgar\MigrationPack::up(),
     * CHEZ14\Ilgar\MigrationPack::pre_migrate(),
     * CHEZ14\Ilgar\MigrationPack::on_migrate()
     * CHEZ14\Ilgar\MigrationPack::post_migrate()
     * @return void
     */
    public function methodAbleToHandleException()
    {
        $stub = $this->getMockForAbstractClass(
            MigrationPacket::class,
            [Runner::instance()],
            '',
            true,
            true,
            true,
            ['pre_migrate', 'post_migrate']
        );

        $stub->expects($this->once())
            ->method('pre_migrate');

        $stub->expects($this->never())
            ->method('post_migrate');

        $stub->expects($this->once())
            ->method('on_migrate')
            ->willThrowException(new Error("Just a generic error"));

        $this->expectException(RuntimeException::class);
        $stub->run();
    }
}
