<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 17.06
 */

namespace AppBundle\Services\Validators;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use function Symfony\Component\Debug\Tests\testHeader;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidator;

class ProductValidator implements Validator
{
    private $skipped = 0;
    private $succesful = 0;
    private $successfulProducts = [];
    private $skippedProducts = [];
    private $productRepository;
    private $symfonyValidator;

    public function __construct(EntityManager $em, SymfonyValidator $validator)
    {
        $this->productRepository = $em->getRepository(Product::class);
        $this->symfonyValidator = $validator;
    }

    /**
     * @return int
     */
    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    /**
     * @return int
     */

    public function getSuccessfulCount(): int
    {
        return $this->succesful;
    }

    /**
     * @param array $products
     */
    public function validate(array $products) : void
    {
        foreach ($products as $product) {
            $tempProduct = $this->productRepository->findOneByCode($product->getCode());
            if (isset($tempProduct)) {
                $tempProduct->setCost($product->getCost());
                $tempProduct->setStock($product->getStock());
                $tempProduct->setName($product->getName());
                $tempProduct->setDescription($product->getDescription());
                $tempProduct->setTimestamp($product->getTimestamp());
                $product = $tempProduct;
            }

            $errors = $this->symfonyValidator->validate($product);

            if (count($errors) >= 1) {
                $errorStr = "";
                foreach ($errors as $error) {
                    $errorStr .= '***' . $error->getMessage() . "\n";
                }
                array_push($this->skippedProducts, ['item' => $product, 'errors' => $errorStr]);
                $this->skipped++;
            } else if ($this->isDublicateCode((int)$product->getCode())) {
                array_push($this->skippedProducts, ['item' => $product, 'errors' => '***Dublicate item' . "\n"]);
                $this->skipped++;
            } else {
                array_push($this->successfulProducts, $product);
                $this->succesful++;
            }
        }


    }

    /**
     * @param int $code
     * @return bool
     */
    private function isDublicateCode(int $code) : bool
    {
        foreach ($this->successfulProducts as $item) {
            if ($code === $item->getCode()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getSuccessfulItems() : array
    {
        return $this->successfulProducts;
    }

    /**
     * @return array
     */
    public function getSkippedItems() : array
    {
        return $this->skippedProducts;
    }

}