<?php
require_once('InstallSQLTrait.php');
require_once('SaveResultTrait.php');

class KnapsackAlgorithm {

    use InstallSQLTrait {
        InstallSQLTrait::setPdo as protected setPdoForInstallSQLTrait;
    }
    use SaveResultTrait {
        InstallSQLTrait::setPdo insteadof SaveResultTrait;
        SaveResultTrait::setPdo as protected setPdoForSaveResultTrait;
    }
    
    use SeedDbTrait {
        SeedDbTrait::setPdo as protected setPdoForSeedDbTrait;
    }

    private $pdo;
    private $table = 'products';
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->setPdoForInstallSQLTrait($pdo);
        $this->setPdoForSaveResultTrait($pdo);
        $this->setPdoForSeedDbTrait($pdo);
    }
    
    public function findOneCollection()
    {
        $stmt = $this->pdo->prepare("CALL `get30products`(@products, @remainingPrice);");
        $stmt->execute();
        $stmt->closeCursor();

        $result1 = $this->pdo
            ->query("SELECT @products AS `str`, @remainingPrice AS `remainingPrice`;")
            ->fetch(PDO::FETCH_ASSOC);
        $remainingPrice = $result1['remainingPrice'];
        $stmt = $this->pdo->prepare("CALL `findProductWithExactPrice`(?, @productId);");
        $stmt->execute([$remainingPrice]);
        $result2 = $this->pdo
            ->query("SELECT @productId AS `productId`;")
            ->fetch(PDO::FETCH_ASSOC);
        if($result2['productId'] == 0) {
            throw new Exception('Not found');
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
            catch(Exception $e) {
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
    }

}
