<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knapsack\ExactCollections;
use Knapsack\Config;
use Knapsack\UpperLimitCollections;

require_once('pdo_config.php');

$config = new Config();
$config->tableName = $table;

$knapsackAlgo = new ExactCollections($pdo, $config);

$totalItems = $knapsackAlgo->getTotalItems();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Knapsack implementation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
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
                            <a class="nav-link active" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                Dashboard <span class="sr-only">(current)</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h1 class="h2"><?php echo $totalItems; ?> total items</h1>
                <canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>
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
                            <tr>
                                <td>0-10</td>
                                <td>10</td>
                                <td>5</td>
                                <td>19</td>
                            </tr>
                            <tr>
                                <td>10-20</td>
                                <td>15</td>
                                <td>consectetur</td>
                                <td>adipiscing</td>
                            </tr>
                            <tr>
                                <td>20-30</td>
                                <td>4</td>
                                <td>nec</td>
                                <td>odio</td>
                            </tr>
                            <tr>
                                <td>30-40</td>
                                <td>7</td>
                                <td>Sed</td>
                                <td>cursus</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
        <table>
            <thead>
            </thead>
            <tbody>
                <tr>
                    <td># items</td>
                    <td><?php echo $totalItems; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["0-100", "100-200", "200-300", "300-400", "400-500"],
                datasets: [{
                    data: [30, 40, 45, 20, 17, 19, 32],
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
                            beginAtZero: false
                        }
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });
    </script>
</body>
</html>