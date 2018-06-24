<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knapsack\ExactCollections;
use Knapsack\Config;
use Knapsack\UpperLimitCollections;
use Knapsack\Statistics;

require_once('pdo_config.php');

$config = new Config();
$config->tableName = $table;

$number = isset($_POST['number']) ? intval($_POST['number']): 1;
$type = isset($_POST['type']) ? intval($_POST['type']): 1;

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
    $itemsData = $statistics->getPricesForItems($items);
    $itemsData = array_map(function ($value) use ($itemsData) {
        return $itemsData[$value] + array('id' => $value);
    }, $items);
    usort($itemsData, function ($item1, $item2) {
        return $item1['price'] - $item2['price'];
    });
    $rowData[] = array(
        'ids' => $items,
        'data' => $itemsData
    );
}

$totalPrices = array();
foreach ($rowData as $collection) {
    $totalPrice = 0;
    foreach ($collection['data'] as $item) {
        $totalPrice += $item['price'];
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

    <?php foreach ($rowData as $i => $collection): ?>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Found collection of <?php echo count($collection['ids']); ?> items</h5>
                <h6>Total price <?php echo array_pop($totalPrices); ?></h6>
                <p class="card-text">
                    <a class="btn btn-primary" data-toggle="collapse" href="#collapse-collection-<?php echo $i; ?>" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Show results
                    </a>
                    <div class="collapse" id="collapse-collection-<?php echo $i; ?>">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>Item Id</th>
                                    <th>Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($collection['data'] as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><?php echo $item['price']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
