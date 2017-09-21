<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 20.01
 */

namespace AppBundle\Classes\ImportMods;

class TestMode implements Mode
{
    public function __construct() {}

    /**
     * @param array $products
     */
    public function import(array $products) : void
    {

    }
}