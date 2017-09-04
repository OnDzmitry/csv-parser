<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 19.58
 */

namespace AppBundle\ImportMods;

interface Mode
{
    function import(array $products);
}