<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knapsack\ExactCollections;
use Knapsack\Config;
use Knapsack\UpperLimitCollections;
use Knapsack\Statistics;

require_once('pdo_config.php');

$config = new Config();
$config->tableName = $table;

$statistics = new Statistics($pdo, $config);
$knapsackAlgo = new ExactCollections($pdo, $config);

$totalItems = 0;
$tableRangeValues = array();
$chartRangeValues = array();

$step = isset($_GET['step']) ? intval($_GET['step']): 25;
$tableStep = $step;

$installed = $knapsackAlgo->isInstalled();

if(!$installed) {
    $shouldInstall = isset($_GET['install']) ? intval($_GET['install']): 0;
    if($shouldInstall) {
        $knapsackAlgo->setInstallScript(__DIR__. '/../install.sql');
        $success = $knapsackAlgo->install();
        header('Location: /', true, 301);
        exit();
    }
}

if($installed) {

    $shouldSeed = isset($_GET['option']) && $_GET['option'] === 'seed' ? 1: 0;
    if($shouldSeed) {
        $knapsackAlgo->seedDb(intval($_GET['total']), intval($_GET['minPrice']), intval($_GET['maxPrice']));
        header('Location: /', true, 301);
        exit();
    }

    $totalItems = $knapsackAlgo->getTotalItems();

    $tableRange = range($tableStep, 500, $tableStep);

    foreach ($tableRange as $max) {
        $tableRangeValues[$max] = $statistics->getTotalItemsInRange($max - $tableStep, $max);
    }
    $chartStep = $step;
    $chartRange = range($chartStep, 500, $chartStep);

    foreach ($chartRange as $max) {
        $chartRangeValues[] = $statistics->getTotalItemsInRange($max - $chartStep, $max);
    }
    $chartRangeLabels = array_map(function ($value) use ($chartStep) {
        return ($value - $chartStep) . '-' . $value;
    }, $chartRange);
}

$randomColors = array_map(function ($value) {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}, $chartRange);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Knapsack implementation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <h1>Knapsack</h1>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                Statistics <span class="sr-only">()</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="find-collections-tab" data-toggle="tab" href="#find-collections" role="tab" aria-controls="find-collections" aria-selected="false">Find Collections</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="install-tab" data-toggle="tab" href="#install" role="tab" aria-controls="install" aria-selected="false">Install & Seed</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="saved-collections-tab" data-toggle="tab" href="#saved-collections" role="tab" aria-controls="contact" aria-selected="false">Saved Collections</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h1 class="h2"><?php echo $totalItems; ?> total items</h1>
                <?php if(!$installed): ?>
                    <div class="alert alert-warning" role="alert">
                        Please press install to initialize your database.
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <button type="button" class="btn btn-success nav-link" data-toggle="tab" href="#install" role="tab">Import options</button>
                        </li>
                    </ul>
                    <hr/>
                <?php endif; ?>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Change step
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="?step=10">10</a>
                                <a class="dropdown-item" href="?step=25">25</a>
                                <a class="dropdown-item" href="?step=50">50</a>
                            </div>
                        </div>
                        <canvas class="my-4 w-100" id="bar-chart" width="900" height="380"></canvas>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>Price Range</th>
                                    <th># items</th>
                                    <th>% total</th>
                                    <th>% Sum</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($tableRangeValues as $max => $total): ?>
                                    <tr>
                                        <td><?php echo $max - $tableStep; ?>-<?php echo $max; ?></td>
                                        <td><?php echo $total; ?></td>
                                        <td><?php echo number_format((float)(100 * $total/$totalItems), 2, '.', ''); ?>%</td>
                                        <td>19</td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <canvas class="my-4 w-100" id="doughnut-chart" width="900" height="380"></canvas>
                    </div>
                    <div class="tab-pane fade" id="find-collections" role="tabpanel" aria-labelledby="find-collections-tab">
                        <hr/>
                        <h2>Find collections <span class="badge badge-secondary">New</span></h2>
                        <hr/>
                        <form id="find-collection" action="findcollection.php" class="form">
                            <input type="hidden" name="option" value="find"/>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="seed-db-option1">Collection type.</label>
                                    <select name="type" class="form-control" id="seed-db-option1" required>
                                        <option value="1">Exact</option>
                                        <option value="2">Upper limit</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="seed-db-option4">How many collection to try to find?</label>
                                    <select name="number" class="form-control" id="seed-db-option4" required>
                                        <option>1</option>
                                        <option>2</option>
                                        <option>5</option>
                                        <option>20</option>
                                        <option>50</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <button type="submit" class="btn btn-primary btn-lg" style="position: absolute;bottom: 0">Find collection</button>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-check">
                                    <input name="save" type="checkbox" class="form-check-input" id="saveCollection">
                                    <label class="form-check-label" for="saveCollection">Save collections</label>
                                </div>
                            </div>
                        </form>
                        <div id="collection-info">

                        </div>
                    </div>
                    <div class="tab-pane fade" id="install" role="tabpanel" aria-labelledby="install-tab">
                        <?php if(!$installed): ?>
                            <div class="alert alert-info" role="alert">
                                Database should be empty!
                            </div>
                            <a role="button" class="btn btn-success" href="?install=1">Init Database(importing install.sql)</a>
                        <?php else: ?>
                            <form class="form">
                                <input type="hidden" name="option" value="seed"/>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="seed-db-option1">Please select max price.</label>
                                        <select name="minPrice" class="form-control" id="seed-db-option1" required>
                                            <option>0</option>
                                            <option>100</option>
                                            <option>200</option>
                                            <option>300</option>
                                            <option>400</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="seed-db-option4">Please select max price.</label>
                                        <select name="maxPrice" class="form-control" id="seed-db-option4" required>
                                            <option>100</option>
                                            <option>200</option>
                                            <option>300</option>
                                            <option>400</option>
                                            <option>500</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="seed-db-option2">How many items to add?</label>
                                        <select name="total" class="form-control" id="seed-db-option2" required>
                                            <option>10</option>
                                            <option>100</option>
                                            <option>500</option>
                                            <option>2000</option>
                                            <option>5000</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <button type="submit" class="btn btn-primary btn-lg" style="position: absolute;bottom: 0">Seed Db</button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="tab-pane fade" id="saved-collections" role="tabpanel" aria-labelledby="saved-collections-tab">
                        <hr/>
                        <h2>Saved collections <span class="badge badge-secondary">New</span></h2>
                        <hr/>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script>
        var ctx = document.getElementById("bar-chart");
        var barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartRangeLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartRangeValues); ?>,
                    lineTension: 0,
                    backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    borderWidth: 4,
                    pointBackgroundColor: '#007bff'
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });
        var ctx = document.getElementById("doughnut-chart");
        var doughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chartRangeLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartRangeValues); ?>,
                    lineTension: 0,
                    backgroundColor: <?php echo json_encode($randomColors); ?>,
                    //backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    borderWidth: 4,
                    pointBackgroundColor: '#007bff'
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Items distribution.'
                }
            }
        });
    </script>
</body>
</html>