<?php

namespace Tests\Integration;

use CHEZ14\Ilgar\Test\Utils\DBSetup;
use PHPUnit\Framework\TestCase;

/**
 * Base Test Class
 * @group null
 */
abstract class BaseTest extends TestCase
{
    /**
     * FatFree Instance
     *
     * @var \Base
     */
    public $f3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->f3 = \F3::instance();
        $this->f3->set('ILGAR.show_log', false);
        $this->f3->set('ILGAR.disable_ob', false);
        DBSetup::setup();
    }
}
