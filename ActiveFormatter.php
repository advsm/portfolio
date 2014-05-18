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
        return "icon/" . $this->icon;
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
     * @return string
     */
    public function getPlatformUrl()
    {
        return 'http://antines.ru/v/' . strtolower($this->getPlatformName());
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (in_array($this->id, [22294, 22295])) {
            return '/v/alpari/petrov-ivan';
        }

        if ($this->id == 67412) {
            return '/v/panteon-finance/stable';
        }

        $platform = strtolower($this->getPlatformName());
        if ($platform === 'panteon') {
            $platform = 'panteon-finance';
        }

        $name = str_replace('_', '-', $this->getName());
        $name = str_replace(' ', '-', $name);
        $name = rtrim($name, '-');

        if ($this->getName() === 'Mill Trade - Золотая 7') {
            return $this->getPlatformUrl() . '-gold7';
        }

        if ($this->getName() === 'памм-фонд-стабильный') {
            $name = 'stable';
        }

        if (strtolower($this->getName()) === 'mmcis index top 20') {
            return '/v/mmcis';
        }

        if ($this->getName() === 'Trade-Bowl(ECNp20)') {
            $name = 'trade-bowl20';
        }

        if ($this->getName() === 'elrid(homeinvestblog)') {
            $name = 'elrid';
        }

        if ($name === 'petrov-ivan') {
            $platform = 'alpari';
        }

        return '/v/' . $platform . '/' . strtolower($name);
    }
}