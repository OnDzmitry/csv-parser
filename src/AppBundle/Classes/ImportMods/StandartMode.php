<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 19.59
 */

namespace AppBundle\Classes\ImportMods;
use Doctrine\ORM\EntityManager;

class StandartMode implements Mode
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $products
     */
    public function import(array $products) : void
    {
        for ($i = 0; $i < count($products); $i++) {
            if ($products[$i]->getId() === null) {
                $this->em->persist($products[$i]);
            }
            if ($i % 100 === 0) {
                $this->em->flush();
            }
        }
        $this->em->flush();
    }
}