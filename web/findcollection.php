<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knapsack\ExactCollections;
use Knapsack\Config;
use Knapsack\UpperLimitCollections;
use Knapsack\Statistics;

require_once('pdo_config.php');

$config = new Config();
$config->tableName = $table;

$number = isset($_GET['number']) ? intval($_GET['number']): 1;
$type = isset($_GET['type']) ? intval($_GET['type']): 1;

$knapsackAlgo = new ExactCollections($pdo, $config);
if($type === 2) {
    $knapsackAlgo = new UpperLimitCollections($pdo, $config);
}

$collections = array();

if($number === 1) {
    try {
        $collections[] = $knapsackAlgo->findOneCollection();
    }
    catch(\Exception $e) {
    }
}
else {
    $collections = $knapsackAlgo->findMultipleCollections($number);
}

$statistics = new Statistics($pdo, $config);

$rowData = array();
foreach ($collections as $collection) {
    $items = explode(',', $collection);
    $rowData[] = array(
        'ids' => $items,
        'data' => $statistics->getPricesForItems($items)
    );
}

$totalPrices = array();
foreach ($rowData as $collection) {
    $totalPrice = 0;
    foreach ($collection['ids'] as $id) {
        $totalPrice += $collection['data'][$id]['price'];
    }
    $totalPrices[] = $totalPrice;
}

?>
<?php if(empty($collections)): ?>
    <div class="alert alert-warning" role="alert">
        No collection found!
    </div>
<?php endif; ?>
<div class="row">

    <?php foreach ($rowData as $collection): ?>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Found collection of <?php echo count($collection['ids']); ?> items</h5>
                <h6>Total price <?php echo array_pop($totalPrices); ?></h6>
                <p class="card-text">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>Item Id</th>
                                <th>Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($collection['ids'] as $id): ?>
                                <tr>
                                    <td><?php echo $id; ?></td>
                                    <td><?php echo $collection['data'][$id]['price']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
