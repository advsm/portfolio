<?php

require_once 'vendor/autoload.php';
require_once 'MonitorFormatter.php';
require_once 'ActiveFormatter.php';

// Инициализация API
$config = new Masterfolio\Config(include 'config.php');
$portfolio = new Masterfolio\Portfolio($config);

// Получение прибыли по портфелю за текущий месяц
$xml = $portfolio->getProfitForPeriod('2014-03-01', '2014-03-31');

// Вывод прибыли
$formatter = new MonitorFormatter($xml);
echo $formatter->render();


