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

$totalItems = $knapsackAlgo->getTotalItems();

$step = isset($_GET['step']) ? intval($_GET['step']): 25;
$tableStep = $step;
$tableRange = range($tableStep, 500, $tableStep);
$tableRangeValues = array();
foreach ($tableRange as $max) {
    $tableRangeValues[$max] = $statistics->getTotalItemsInRange($max - $tableStep, $max);
}
$chartStep = $step;
$chartRange = range($chartStep, 500, $chartStep);
$chartRangeValues = array();
foreach ($chartRange as $max) {
    $chartRangeValues[] = $statistics->getTotalItemsInRange($max - $chartStep, $max);
}
$chartRangeLabels = array_map(function ($value) use ($chartStep) {
    return ($value - $chartStep). '-' . $value;
}, $chartRange);

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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
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
                            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Saved Collections</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h1 class="h2"><?php echo $totalItems; ?> total items</h1>
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

                    </div>
                    <div class="tab-pane fade" id="install" role="tabpanel" aria-labelledby="install-tab">...</div>
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