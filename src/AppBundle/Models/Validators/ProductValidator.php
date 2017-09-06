<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 17.06
 */

namespace AppBundle\Models\Validators;


use AppBundle\Entity\Product;

class ProductValidator implements Validator
{
    private $skipped = 0;
    private $succesful = 0;
    private $container;
    private $successfulProducts = [];
    private $skippedProducts = [];
    private $productRepository;

    public function __construct($container)
    {
        $this->container = $container;
        $this->productRepository = $container->get('doctrine.orm.default_entity_manager')->getRepository(Product::class);
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    public function getSuccessfulCount(): int
    {
        return $this->succesful;
    }

    public function validate(array $products)
    {
        $validator = $this->container->get('validator');

        foreach ($products as $product) {
            $tempProduct = $this->productRepository->findOneByCode($product->getCode());

            if (isset($tempProduct)) {
                $product->setId($tempProduct->getId());
                $product->setAddAt($tempProduct->getAddAt());
                $product->setTimestamp($tempProduct->getTimestamp());
            }

            $errors = $validator->validate($product);

            if (count($errors) >= 1) {
                array_push($this->skippedProducts, ['item'=>$product, 'errors'=> $errors]);
                $this->skipped++;
            } else {
                array_push($this->successfulProducts, $product);
                $this->succesful++;
            }
        }
    }

    public function getSuccessfulItems() : array
    {
        return $this->successfulProducts;
    }

    public function getSkippedItems() : array
    {
        return $this->skippedProducts;
    }

}