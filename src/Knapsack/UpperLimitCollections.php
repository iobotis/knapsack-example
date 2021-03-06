<?php

namespace Knapsack;

use Knapsack\Config;
use Knapsack\KnapsackInterface;
use Knapsack\Traits\InstallSQLTrait;
use Knapsack\Traits\SaveResultTrait;
use Knapsack\Traits\SeedDbTrait;

class UpperLimitCollections implements KnapsackInterface {

    use InstallSQLTrait;

    use SaveResultTrait {
        InstallSQLTrait::setPdo insteadof SaveResultTrait;
        SaveResultTrait::setPdo as protected setPdoForSaveResultTrait;
    }

    private $pdo;

    public function __construct(\PDO $pdo, Config $config)
    {
        $this->pdo = $pdo;
        $this->setPdo($pdo);
    }
    
    public function findOneCollection()
    {
        $stmt = $this->pdo->prepare("CALL `get30products`(@items, @remainingPrice);");
        $stmt->execute();
        $stmt->closeCursor();
        $result1 = $this->pdo
            ->query("SELECT @items AS `str`, @remainingPrice AS `remainingPrice`;")
            ->fetch(\PDO::FETCH_ASSOC);
        $remainingPrice = $result1['remainingPrice'];
        $stmt = $this->pdo->prepare("CALL `findProductClosestToPrice`(?, @productId);");
        $stmt->execute([$remainingPrice]);
        $result2 = $this->pdo
            ->query("SELECT @productId AS `productId`;")
            ->fetch(\PDO::FETCH_ASSOC);
        if($result2['productId'] == 0) {
            throw new \Exception('Not found');
        }
        return $result1['str'] . ',' . $result2['productId'];
    }
    
    public function findMultipleCollections($total = 10)
    {
        $collections = [];
        for($i = 0; $i < $total; $i++) {
            try {
                $collections[] = $this->findOneCollection();
            }
            catch(\Exception $e) {
            }
        }
        return $collections;
    }

    public function findAndSaveCollections($total = 10)
    {
        $collections = $this->findMultipleCollections($total);
        foreach($collections as $collection) {
            $items = explode(',', $collection);
            $this->insertToResults($collection, count($items), 500);
        }
        return $collections;
    }
}
