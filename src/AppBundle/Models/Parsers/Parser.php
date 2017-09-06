<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 16.05
 */

namespace AppBundle\Models\Parsers;


interface Parser
{
    public function parse();
    public function getProcessedCount() : int;
}