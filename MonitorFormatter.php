<?php

/**
 * Class MonitorFormatter
 *
 * Класс с наобором вспомогательных методов для вывода прибыли по портфелю за определенный период
 */
class MonitorFormatter
{
    /**
     * Строка в формате XML с результатом выполнения команды getmonitor.
     *
     * @var SimpleXMLElement $data
     */
    private $data;

    /**
     * Список активов.
     *
     * @var ActiveFormatter[]
     */
    private $actives;

    /**
     * Конфигурационный массив приложения
     *
     * @var array
     */
    private $config;

    public function __construct(SimpleXMLElement $data) {
        $this->data = $data;

        $actives = [];
        foreach ($data->data->accounts->account as $account) {
            foreach ($account->assets->asset as $asset) {
                $active = new ActiveFormatter();

                $active->id              = $asset->id->__toString();
                $active->platformName    = $asset->platform_name->__toString();
                $active->name            = $asset->name->__toString();
                $active->previousBalance = $asset->previous_balance->USD->__toString();
                $active->balance         = $asset->balance->USD->__toString();
                $active->delta           = $asset->delta->USD->__toString();
                $active->relativeDelta   = $asset->relative_delta->USD->__toString();
                $active->icon            = $account->icon->__toString();
                $active->shift          = $asset->shifts->USD->__toString();

                $actives[ $active->id ] = $active;
                $sorted [ $active->id ] = $active->relativeDelta;
            }
        }

        arsort($sorted);
        foreach ($sorted as $id => $delta) {
            $sorted[$id] = $actives[$id];
        }

        $aggregated = [];
        foreach ($sorted as $active) {
            $name = $active->name;
            if (isset($aggregated[$name])) {
                $a = $aggregated[$name];
                $a->balance         += $active->balance;
                $a->previousBalance += $active->previousBalance;
                $a->delta           += $active->delta;
                $a->relativeDelta    = round(($a->relativeDelta + $active->relativeDelta) / 2, 2);

                continue;
            }

            $aggregated[$name] = $active;
        }

        $this->actives = $aggregated;
    }

    /**
     * Рисует таблицу доходности по предоставленным данным.
     *
     * @return string
     */
    public function render()
    {
        ob_start();
        echo "<table class='zebra'>";
        echo "<tr>
        <th>Площадка, ПАММ</th>
        <th>Было</th>
        <th>Стало</th>
        <th>Ввод/Вывод</th>
        <th>Доход в $</th>
        <th>Доход в %</th>
        </tr>";

        foreach ($this->actives as $active) {
            $class = 'inv-loss';
            if ($active->isProfit()) {
                $class = 'inv-success';
            }

            echo "<tr class='$class'>";

            echo "<td><a href='{$active->getPlatformUrl()}'><img width='20' height='20' src='{$active->getIcon()}' /></a>";
            echo " <a href='{$active->getUrl()}'>{$active->getName()}</a></td>";
            echo "<td>" . $this->formatBalance($active->getPreviousBalance()) . "</td>";
            echo "<td>" . $this->formatBalance($active->getBalance()) . "</td>";
            echo "<td>" . $this->formatBalance($active->shift) . "</td>";
            echo "<td class='delta'>" . $this->formatBalance($active->getDelta())             . "</td>";
            echo "<td class='delta'>" . $this->formatDelta($active->getRelativeDelta())     . "</td>";

            echo "</tr>";
        }

        echo "<tr class='inv-success'><td>Всего</td>
        <td>" . $this->formatBalance($this->data->data->previous_balance->USD->__toString()) . "</td>
        <td>" . $this->formatBalance($this->data->data->total->USD->__toString()) . "</td>
        <td>" . $this->formatBalance($this->data->data->shifts->USD->__toString()) . "</td>
        <td class='delta'>" . $this->formatBalance($this->data->data->delta->USD->__toString()) . "</td>
        <td class='delta'>" . $this->formatDelta($this->data->data->relative_delta->USD->__toString()) . "</td></tr>";
        echo "</table>";

        return ob_get_clean();
    }

    /**
     * Рисует таблицу доходности по предоставленным данным.
     *
     * @return string
     */
    public function renderByBroker()
    {
        ob_start();
        echo "<table class='zebra'>";
        echo "<tr>
        <th>&nbsp;</th>
        <th>Было</th>
        <th>Стало</th>
        <th>Ввод/Вывод</th>
        <th>Доход в $</th>
        <th>Доход в %</th>
        </tr>";

        $sortedByBrokers = [];
        foreach ($this->actives as $active) {
            $sortedByBrokers[$active->getPlatformName()][] = $active;
        }

        foreach ($sortedByBrokers as $brokerName => $actives) {

            $allBrokerActives = '';
            $totalPreviousBalance = 0;
            $totalBalance         = 0;
            $totalShifts          = 0;
            $totalDelta           = 0;
            $relativeDeltas       = [];
            foreach ($actives as $active) {
                $class = 'inv-loss';
                if ($active->isProfit()) {
                    $class = 'inv-success';
                }

                $allBrokerActives .= "<tr class='$class'>";

                $allBrokerActives .= "<td><a href='{$active->getUrl()}'>{$active->getName()}</a></td>";
                $allBrokerActives .= "<td>" . $this->formatBalance($active->getPreviousBalance()) . "</td>";
                $allBrokerActives .= "<td>" . $this->formatBalance($active->getBalance()) . "</td>";
                $allBrokerActives .= "<td>" . $this->formatBalance($active->shift) . "</td>";
                $allBrokerActives .= "<td class='delta'>" . $this->formatBalance($active->getDelta())             . "</td>";
                $allBrokerActives .= "<td class='delta'>" . $this->formatDelta($active->getRelativeDelta())     . "</td>";
                $allBrokerActives .= "</tr>";

                $totalPreviousBalance += $active->previousBalance;
                $totalBalance         += $active->balance;
                $totalShifts          += $active->shift;
                $totalDelta           += $active->getDelta();
                $relativeDeltas[]      = $active->getRelativeDelta();
            }

            $totalRelativeDelta    = round(array_sum($relativeDeltas) / count($relativeDeltas), 2);

            echo "<tr>";
            echo "<th><a href='{$active->getPlatformUrl()}'><img width='20' height='20' src='{$active->getIcon()}' /></a> &nbsp;&nbsp; <a href='{$active->getPlatformUrl()}'>{$brokerName}</a></th>";
            echo "<th>" . $this->formatBalance($totalPreviousBalance) . "</th>";
            echo "<th>" . $this->formatBalance($totalBalance) . "</th>";
            echo "<th>" . $this->formatBalance($totalShifts) . "</th>";
            echo "<th class='delta'>" . $this->formatBalance($totalDelta)             . "</th>";
            echo "<th class='delta'>" . $this->formatDelta($totalRelativeDelta)     . "</th>";
            echo "</tr>";

            echo $allBrokerActives;
        }

        echo "<tr class='inv-success'><th>Всего</th>
        <th>" . $this->formatBalance($this->data->data->previous_balance->USD->__toString()) . "</th>
        <th>" . $this->formatBalance($this->data->data->total->USD->__toString()) . "</th>
        <th>" . $this->formatBalance($this->data->data->shifts->USD->__toString()) . "</th>
        <th class='delta'>" . $this->formatBalance($this->data->data->delta->USD->__toString()) . "</th>
        <th class='delta'>" . $this->formatDelta($this->data->data->relative_delta->USD->__toString()) . "</th></tr>";
        echo "</table>";

        return ob_get_clean();
    }

    /**
     * Возвращает баланс актива, отформатированный в завасимости от того, прибыль это или убыток.
     *
     * @param string $balance
     * @return string
     */
    private function formatBalance($balance)
    {
        if ($balance >= 0) {
            return '$' . $balance;
        }

        return '- $' . abs($balance);
    }

    /**
     * Возвращает процент прибыли или убытка актива, отформатированный для отображения.
     *
     * @param string $delta
     * @return string
     */
    private function formatDelta($delta)
    {
        if ($delta >= 0) {
            return $delta . '%';
        }

        return '- ' . abs($delta) . '%';
    }

    /**
     * Устанавливает конфиг.
     *
     * @param array $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        foreach ($this->actives as $active) {
            $active->setConfig($config);
        }

        return $this;
    }
}
