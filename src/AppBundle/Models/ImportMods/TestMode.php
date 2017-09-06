<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 20.01
 */

namespace AppBundle\Models\ImportMods;
use AppBundle\ImportMods;

class TestMode implements Mode
{
    public function __construct() {}

    public function import(array $products)
    {

    }
}