<?php

namespace Knapsack\Traits;

trait SaveResultTrait {

    private $pdo;
    protected function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    protected function insertToResults($collection, $totalItems, $price)
    {
        $statement = $this->pdo->prepare("INSERT INTO results(totalItems, totalPrice, items)
             VALUES(:totalItems, :totalPrice, :items)");
        $statement->execute(array(
            "totalItems" => $totalItems,
            "totalPrice" => $price,
            "items" => $collection
        ));
    }
}
