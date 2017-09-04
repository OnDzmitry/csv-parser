<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 19.59
 */

namespace AppBundle\ImportMods;
use AppBundle\ImportMods;

class StandartMode implements Mode
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function import(array $products) {

    }
}