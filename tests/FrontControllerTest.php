<?php
namespace Test\Lucinda\ConsoleSTDOUT;

use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;

class FrontControllerTest
{
    public function run(): array
    {
        $logFile = sys_get_temp_dir()."/console-stdout-front-controller-".uniqid("", true).".log";
        @unlink($logFile);

        $command = sprintf(
            "FC_EVENT_LOG=%s php %s %s %s %s",
            escapeshellarg($logFile),
            escapeshellarg(__DIR__."/../tools/front_controller_runner.php"),
            escapeshellarg(__DIR__."/fixtures/front-controller.xml"),
            escapeshellarg("test"),
            escapeshellarg("world")
        );
        $output = trim((string) shell_exec($command));
        $events = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        @unlink($logFile);

        return [
            (new Strings($output))->assertEquals("Test: world"),
            (new Arrays($events))->assertEquals(["start", "application", "request", "end"])
        ];
    }
}
