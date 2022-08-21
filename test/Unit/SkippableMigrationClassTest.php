<?php

namespace CHEZ14\Ilgar\Test\Unit;

use CHEZ14\Ilgar\Runner;
use CHEZ14\Ilgar\Test\Utils\InvokeMethod;
use PHPUnit\Framework\TestCase;

/**
 * SkippableMigrationClassTest
 *
 * Confirms for migration selector able to skip and add certain migration
 * version.
 *
 * {@see CHEZ14\Ilgar\Runner::cleanMigration} should be able to pick the
 * migration that are has not been ran yet.
 *
 * @group Runners
 */
class SkippableMigrationClassTest extends TestCase
{
    use InvokeMethod;

    protected $runnerNamespace = "CHEZ14\\Ilgar";

    /**
     * @test
     *
     * Able to correctly pick all of the migration when there's nothing on the
     * db.
     *
     * @testdox Making sure migration cleaner functions able to pick migration
     * that are not ran yet.
     *
     * @dataProvider migrationTestCaseDataProvider
     * @covers CHEZ14\Ilgar\Runner::cleanMigration
     *
     * @return void
     */
    public function updateFromScratch($existingMigration, $migratableMigration, $expectation)
    {
        $cleanedMigration = $this->invokeMethod(
            Runner::instance(),
            "cleanMigration",
            [$migratableMigration, $existingMigration]
        );

        $this->assertEqualsCanonicalizing($expectation, $cleanedMigration);
    }

    /**
     * Provides data for migration cleaners.
     *
     * @return array
     */
    public function migrationTestCaseDataProvider()
    {
        return [
            'number: Checks for empty migration' => [
                "existing" => [],
                "migratable" => [
                    ['version' => '1',],
                    ['version' => '2',],
                    ['version' => '3',],
                ],
                'expectation' => [
                    ['version' => '1',],
                    ['version' => '2',],
                    ['version' => '3',],
                ]
            ],
            'number: Checks for some migration that has run previously' => [
                "existing" => [
                    ['version' => '1',],
                    ['version' => '2',],
                ],
                "migratable" => [
                    ['version' => '3',],
                ],
                'expectation' => [
                    ['version' => '3',],
                ]
            ],
            'number: Further check for migration that has run previously' => [
                "existing" => [
                    ['version' => '1',],
                ],
                "migratable" => [
                    ['version' => '2',],
                    ['version' => '3',],
                ],
                'expectation' => [
                    ['version' => '2',],
                    ['version' => '3',],
                ]
            ],
            "number: Check for new migration that have version in the middle of current migration" => [
                "existing" => [
                    ['version' => '1',],
                    ['version' => '3',],
                ],
                "migratable" => [
                    ['version' => '2',],
                    ['version' => '4',],
                ],
                'expectation' => [
                    ['version' => '2',],
                    ['version' => '4',],
                ]
            ],
            "number: Check for new migration that have version in the middle of current migration" => [
                "existing" => [
                    ['version' => '1',],
                    ['version' => '2',],
                    ['version' => '3',],
                    ['version' => '4',],
                ],
                "migratable" => [],
                'expectation' => []
            ],
            "number: Migration should not pick things that has been run" => [
                "existing" => [
                    ['version' => '1',],
                    ['version' => '2',],
                    ['version' => '3',],
                    ['version' => '4',],
                ],
                "migratable" => [
                    ['version' => '1',],
                    ['version' => '2',],
                    ['version' => '3',],
                    ['version' => '4',],
                ],
                'expectation' => []
            ],
            "number: Migration should pick things that has not been run in the list of ran migrations" => [
                "existing" => [
                    ['version' => '1',],
                    ['version' => '2',],
                    ['version' => '4',],
                ],
                "migratable" => [
                    ['version' => '1',],
                    ['version' => '2',],
                    ['version' => '3',],
                    ['version' => '4',],
                ],
                'expectation' => [
                    ['version' => '3',],
                ]
            ],

            // date version (date taken from E7 AO Episode Airing.)
            "date: Checks for empty" => [
                "existing" => [],
                "migratable" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ],
                'expectation' => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ]
            ],
            "date: Checks for migration that has been ran previously" => [
                "existing" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],
                ],
                "migratable" => [
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ],
                'expectation' => [
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ]
            ],
            "date: Checks for something in the middle" => [
                "existing" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],
                    ['version' => '20120601150526',],
                ],
                "migratable" => [
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ],
                'expectation' => [
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ]
            ],
            "date: Checks for no migratable thingy." => [
                "existing" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ],
                "migratable" => [],
                'expectation' => []
            ],
            "date: Should not migrate existing migration" => [
                "existing" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ],
                "migratable" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                    ['version' => '20120622150526',],
                ],
                'expectation' => []
            ],
            "date: Should not migrate existing migration, but will migrate one that are not there" => [
                "existing" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',],

                    ['version' => '20120622150526',],
                ],
                "migratable" => [
                    ['version' => '20120413131008',],
                    ['version' => '20120420131015',],
                    ['version' => '20120427131022',],
                    ['version' => '20120504131029',],
                    ['version' => '20120511131105',], // exist

                    ['version' => '20120518150526',], // not exist
                    ['version' => '20120525150526',], // not exist
                    ['version' => '20120601150526',], // not exist
                    ['version' => '20120608150526',], // not exist

                    ['version' => '20120622150526',], // exist
                ],
                'expectation' => [
                    ['version' => '20120518150526',],
                    ['version' => '20120525150526',],
                    ['version' => '20120601150526',],
                    ['version' => '20120608150526',],
                ]
            ],
        ];
    }
}
