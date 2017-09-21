<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 17.12
 */
namespace AppBundle\Classes;

class MessageConsoleHelper
{
    /**
     * @param int $processed
     * @param int $successful
     * @param int $skipped
     * @return string
     */
    static public function getProcessEndMessage(int $processed, int $successful, int $skipped) : string
    {
        return 'Process successful!' .
            'Processed ' . $processed . "\n" .
            'Successful ' . $successful . "\n" .
            'Skipped ' . $skipped . "\n";
    }

    /**
     * @param array $failItems
     * @return string
     */
    static public function getFailItemsMessage(array $failItems) : string
    {
        $resutlStr = "Items which fail to be inserted correctly:\n";
        foreach ($failItems as $record) {
            $resutlStr .= $record['item'] . "\n" . $record['errors'];
        }
        return $resutlStr;
    }
}