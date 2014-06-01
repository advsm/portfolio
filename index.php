<?php

require_once 'vendor/autoload.php';
require_once 'MonitorFormatter.php';
require_once 'ActiveFormatter.php';

// Инициализация API
$config = new Masterfolio\Config(require 'config/application.php');
$portfolio = new Masterfolio\Portfolio($config);

$requestFrom = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$requestTo   = isset($_GET['to'])   ? $_GET['to']   : date('Y-m-t');

$timestampFrom = strtotime($requestFrom);
$timestampTo   = strtotime($requestTo);

if ($timestampFrom > $timestampTo) {
    $t             = $timestampFrom;
    $timestampFrom = $timestampTo;
    $timestampTo   = $t;
    unset($t);
}

if (!$timestampFrom || !$timestampTo) {
    $from = date('Y-m-01');
    $to   = date('Y-m-t');
} else {
    $from = date('Y-m-d', $timestampFrom);
    $to   = date('Y-m-d', $timestampTo);

    if (!$from || !$to) {
        $from = date('Y-m-01');
        $to   = date('Y-m-t');
    }
}


// Получение прибыли по портфелю за текущий месяц
$xml = $portfolio->getProfitForPeriod($from, $to);


// Вывод прибыли
$formatter = new MonitorFormatter($xml);
$formatter->setConfig($config);

?>

<meta charset='utf-8' />
<link rel="stylesheet" href="<?= $config['baseUrl'] ?>/css/zebra.css" />
<link rel="stylesheet" href="<?= $config['baseUrl'] ?>/css/form.css" />

<div id="portfolio">
    <form action="" method="get">
        <input type="text" name="from" value="<?= (isset($_GET['from']) ? $from : '') ?>" /> -
        <input type="text" name="to"   value="<?= (isset($_GET['to'])   ? $to   : '') ?>" />
        <input type="submit" value="Показать за выбранный период" />
    </form>
</div>

<?php
echo $formatter->render();


