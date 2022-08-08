<?php

use Google\Cloud\Logging\LoggingClient;

class GcpLogTool
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /** Write a log message via the Stackdriver Logging API.
     *
     * @param string $projectId The Google project ID.
     * @param string $loggerName The name of the logger.
     * @param string $message The log message.
     */
    function write_log($projectId, $loggerName, $message)
    {
        $logging = new LoggingClient(['projectId' => $projectId]);
        $logger = $logging->logger($loggerName, [
            'resource' => [
                'type' => 'gcs_bucket',
                'labels' => [
                    'bucket_name' => 'my_bucket'
                ]
            ]
        ]);
        $entry = $logger->entry($message);
        $logger->write($entry);
        printf("Wrote a log to a logger '%s'." . PHP_EOL, $loggerName);
    }
}