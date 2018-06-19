<?php

namespace Knapsack\Traits;

use Knapsack\Config;

trait InstallSQLTrait {

    private $pdo;

    private $sqlScript = 'install.sql';

    /**
     * @var Config
     */
    private $config;

    private $error;

    protected function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    public function setInstallScript($sqlScript)
    {
        $this->sqlScript = $sqlScript;
    }

    public function install()
    {
        if(!$this->isInstalled()) {
            $sql = file_get_contents($this->sqlScript);
            $sql = str_replace("%items%", $this->config->tableName, $sql);
            $success = $this->pdo->exec($sql);

            if($success === false) {
                $this->error = $this->pdo->errorInfo();
                return false;
            }
        }
        return true;
    }

    public function isInstalled()
    {
        try {
            $result = $this->pdo->query("SELECT 1 FROM " . $this->config->tableName . " LIMIT 1");
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            // We got an exception == table not found
            return false;
        }
        return false;
    }

    public function getError()
    {
        return $this->error;
    }
}
