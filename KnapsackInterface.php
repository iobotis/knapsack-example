<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/6/2018
 * Time: 7:52 μμ
 */

interface KnapsackInterface
{

    public function __construct(PDO $pdo, Config $config);

    public function findOneCollection();

    public function findMultipleCollections($total = 10);

    public function findAndSaveCollections($total = 10);
}