<?php

namespace CHEZ14\Ilgar\Test\Unit;

use CHEZ14\Ilgar\Runner;
use CHEZ14\Ilgar\Test\Utils\InvokeMethod;
use F3;
use PHPUnit\Framework\TestCase;

/**
 * Migration List Discorvery Test.
 *
 * Making sure things we load the correct file.
 *
 * @group Runners
 */
class MigrationDiscoveryTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;
    use InvokeMethod;

    protected $runnerNamespace = "CHEZ14\\Ilgar";
    protected $initialized = false;


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

        $isFile = $this->getFunctionMock($this->runnerNamespace, "is_file");
        $isFile->expects($this->any())->willReturn(true);
        $basename = $this->getFunctionMock($this->runnerNamespace, "basename");
        $basename->expects($this->any())->willReturnCallback(
            function ($file) {
                return $file;
            }
        );
    }


    /**
     * @test
     * @testdox  scanMigrationFolder shold be able to parse files with timestamp
     * versioning.
     *
     * @covers \CHEZ14\Ilgar\Runner::scanMigrationFolder
     * @return void
     */
    public function properlyParseTimestampFilename()
    {
        $folderList = [
            ".",
            "..",
            "201110022300-DepartureAndFriends.php",
        ];


        // Stub the scandir to sabotage the folder scanning thing.
        $scandir = $this->getFunctionMock($this->runnerNamespace, "scandir");
        $scandir->expects($this->once())->willReturn($folderList);

        $ilgarRunner = Runner::instance();
        $scannedDir = $this->invokeMethod($ilgarRunner, "scanMigrationFolder");

        $this->assertCount(1, $scannedDir);
        $this->assertEquals("201110022300-DepartureAndFriends.php", $scannedDir[0]['filename']);
        $this->assertEquals("201110022300", $scannedDir[0]['version']);
        $this->assertEquals("Migration\DepartureAndFriends", $scannedDir[0]['name']);
    }

    /**
     * @test
     * @testdox  scanMigrationFolder shold not parse files with invalid
     * formatting in timestamp versioning format.
     *
     * @covers \CHEZ14\Ilgar\Runner::scanMigrationFolder
     * @return void
     */
    public function properlyNotParseInvalidTimestampFilename()
    {
        $folderList = [
            ".",
            "..",
            "201110092300_TestOfTests.php",
            "201110162300-RivalsForSurvival.vue",
            "201110232300HopeAndAmbition.php",
            "HisokaIsSneaky.html",
        ];


        // Stub the scandir to sabotage the folder scanning thing.
        $scandir = $this->getFunctionMock($this->runnerNamespace, "scandir");
        $scandir->expects($this->once())->willReturn($folderList);

        $ilgarRunner = Runner::instance();
        $scannedDir = $this->invokeMethod($ilgarRunner, "scanMigrationFolder");

        $this->assertEmpty($scannedDir);
    }

    /**
     * @test
     * @testdox  scanMigrationFolder shold be able to parse files with number
     * versioning.
     *
     * @covers \CHEZ14\Ilgar\Runner::scanMigrationFolder
     * @return void
     */
    public function properlyParseNumberFilename()
    {
        $folderList = [
            ".",
            "..",
            "1-DepartureAndFriends.php",
        ];


        // Stub the scandir to sabotage the folder scanning thing.
        $scandir = $this->getFunctionMock($this->runnerNamespace, "scandir");
        $scandir->expects($this->once())->willReturn($folderList);

        $ilgarRunner = Runner::instance();
        $scannedDir = $this->invokeMethod($ilgarRunner, "scanMigrationFolder");

        $this->assertCount(1, $scannedDir);
        $this->assertEquals("1-DepartureAndFriends.php", $scannedDir[0]['filename']);
        $this->assertEquals("1", $scannedDir[0]['version']);
        $this->assertEquals("Migration\DepartureAndFriends", $scannedDir[0]['name']);
    }

    /**
     * @test
     * @testdox  scanMigrationFolder shold not parse files with invalid number
     * versioning format.
     *
     * @covers \CHEZ14\Ilgar\Runner::scanMigrationFolder
     * @return void
     */
    public function properlyNotParseInvalidNumberFilename()
    {
        $folderList = [
            ".",
            "..",
            "2_TestOfTests.php",
            "3-RivalsForSurvival.vue",
            "4HopeAndAmbition.php",
            "HisokaIsSneaky.html",
        ];


        // Stub the scandir to sabotage the folder scanning thing.
        $scandir = $this->getFunctionMock($this->runnerNamespace, "scandir");
        $scandir->expects($this->once())->willReturn($folderList);

        $ilgarRunner = Runner::instance();
        $scannedDir = $this->invokeMethod($ilgarRunner, "scanMigrationFolder");

        $this->assertCount(0, $scannedDir);
    }


    /**
     * @depends properlyParseTimestampFilename, properlyNotParseInvalidTimestampFilename
     * @test
     *
     * @covers \CHEZ14\Ilgar\Runner::scanMigrationFolder
     * @return void
     */
    public function scanFolderWithProperMultipleTimestampName()
    {
        // Test for folder usage
        $folderList = [
            ".",
            "..",
            "201110022300-DepartureAndFriends.php", // should be detected
            "201110092300-TestOfTests.php", // should be detected
            "201110162300-RivalsForSurvival.php", // should be detected
            "201110232300-HopeAndAmbition.php", // should be detected
            "201110302300-HisokaIsSneaky.html", // should NOT be detected
            "201111062300-ASurprisingChallenge.txt", // should NOT be detected
            "201111132300-ShowdownOnTheAirship.air", // should NOT be detected
            "201111202300_DecisionByMajority.php", // should NOT be detected
            "201111272300-BewareOfPrisoners.php", // should be detected
        ];

        // Stub the scandir to sabotage the folder scanning thing.
        $scandir = $this->getFunctionMock($this->runnerNamespace, "scandir");
        $scandir->expects($this->once())->willReturn($folderList);

        $ilgarRunner = Runner::instance();
        $scannedDir = $this->invokeMethod($ilgarRunner, "scanMigrationFolder");

        $this->assertCount(5, $scannedDir);
    }
}
