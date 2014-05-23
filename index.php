<?php

require_once 'vendor/autoload.php';
require_once 'MonitorFormatter.php';
require_once 'ActiveFormatter.php';

// Инициализация API
$config = new Masterfolio\Config(require 'config/application.php');
$portfolio = new Masterfolio\Portfolio($config);

// Получение прибыли по портфелю за текущий месяц
$xml = $portfolio->getProfitForPeriod('2014-05-01', '2014-05-31');


// Вывод прибыли
$formatter = new MonitorFormatter($xml);
$formatter->setConfig($config);

echo "<meta charset='utf-8' />";
echo "<link rel='stylesheet' href='{$config['baseUrl']}/css/zebra.css' />";
echo $formatter->render();


