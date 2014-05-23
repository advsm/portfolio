<?php

/**
 * Class ActiveFormatter
 *
 * Класс с наобором вспомогательных методов для отображения актива.
 */
class ActiveFormatter
{
    /**
     * Имя брокерской площадки.
     *
     * @var string
     */
    public $platformName;

    /**
     * Имя ПАММ счета/программы ДУ.
     *
     * @var string
     */
    public $name;

    /**
     * Возвращает баланс на начало периода.
     *
     * @var string
     */
    public $previousBalance;

    /**
     * Возвращает баланс на конец периода.
     *
     * @var string
     */
    public $balance;

    /**
     * Возвращает прибыль или убыток за период.
     *
     * @var string
     */
    public $delta;

    /**
     * Возвращает прибыль или убыток за период в процентах.
     *
     * @var string
     */
    public $relativeDelta;

    /**
     * Название иконки брокера.
     *
     * @var string
     */
    public $icon;

    /**
     * Суммарное значение операций ввода-вывода за период
     *
     * @var float
     */
    public $shift;

    /**
     * Конфигурационный массив приложения
     *
     * @var array
     */
    private $config;

    /**
     * @return string
     */
    public function getPlatformName()
    {
        return $this->platformName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = trim(
            str_replace(
                ['FX-Trend', 'Panteon:', '(panteon)', 'Alpari', 'FXOpen', 'Pamm', '2.0'],
                '',
                $this->name
            )
        );

        $name = preg_replace('/[0-9]{3,}/uis', '', $name);
        $name = rtrim($name, ':');
        $name = rtrim($name, '-');
        return $name;
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return string
     */
    public function getPreviousBalance()
    {
        return $this->previousBalance;
    }

    /**
     * @return string
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * @return string
     */
    public function getRelativeDelta()
    {
        return $this->relativeDelta;
    }

    /**
     * Возвращает ссылку на иконку брокера актива.
     *
     * @return string
     */
    public function getIcon()
    {
        return "{$this->config['baseUrl']}/icon/" . $this->icon;
    }

    /**
     * Возвращает TRUE, если актив принес прибыль за период.
     *
     * @return boolean
     */
    public function isProfit()
    {
        if ($this->delta >= 0) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает URL на брокера.
     *
     * @return string
     */
    public function getPlatformUrl()
    {
        return $this->config['brokers'][$this->getPlatformName()]['url'];
    }

    /**
     * Возвращает партнерский URL на актив.
     *
     * @return string
     */
    public function getUrl()
    {
        $pammId = null;
        if (preg_match('/[0-9]{4,10}/', $this->name, $matches)) {
            $pammId = $matches[0];
        }

        if (!$pammId) {
            $pamms = $this->config['brokers'][$this->getPlatformName()]['pamms'];
            if (!isset($pamms[$this->name])) {
                return $this->getPlatformUrl();
            }

            $pammId = $pamms[$this->name];
        }

        $pammUrl = $this->config['brokers'][$this->getPlatformName()]['pammUrl'];
        $pammUrl = str_replace('{pamm}', $pammId, $pammUrl);
        return $pammUrl;
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
        return $this;
    }
}