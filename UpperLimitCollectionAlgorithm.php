<?php
require_once('Config.php');
require_once('KnapsackInterface.php');
require_once('InstallSQLTrait.php');

class UpperLimitCollectionAlgorithm implements KnapsackInterface {

    use InstallSQLTrait;

    private $pdo;
    private $table = 'products';
    
    public function __construct(PDO $pdo, Config $config)
    {
        $this->pdo = $pdo;
        $this->setPdo($pdo);
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
        $stmt = $this->pdo->prepare("CALL `findProductClosestToPrice`(?, @productId);");
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
}
