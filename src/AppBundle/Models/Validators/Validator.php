<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 17.47
 */

namespace AppBundle\Models\Validators;


interface Validator
{
    public function validate(array $items);
    public function getSkippedCount() : int;
    public function getSuccessfulCount() : int;
    public function getSuccessfulItems() : array;
    public function getSkippedItems() : array;
}