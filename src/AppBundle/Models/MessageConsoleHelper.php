<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 17.12
 */
namespace AppBundle\Models;

class MessageConsoleHelper
{
    static public function getProcessEndMessage(int $processed, int $successful, int $skipped)
    {
        return 'Process successful!' .
            'Processed ' . $processed . "\n" .
            'Successful ' . $successful . "\n" .
            'Skipped ' . $skipped . "\n";
    }

    static public function getFailItemsMessage(array $failItems)
    {
        $resutlStr = "Items which fail to be inserted correctly:\n";
        foreach ($failItems as $record) {
            $resutlStr .= $record['item'] . "\n";
            foreach ($record['errors'] as $error) {
                $resutlStr .= '***' . $error->getMessage() . "\n";
            }
        }
        return $resutlStr;
    }
}