<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 19.59
 */

namespace AppBundle\Models\ImportMods;
use AppBundle\ImportMods;
use League\Csv\Exception;

class StandartMode implements Mode
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function import(array $products)
    {
        foreach ($products as $product) {
            $this->em->persist($product);
        }
        $this->em->flush();
    }
}