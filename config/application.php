<?php

$config = [
    'baseUrl' => '/monitor',
    'brokers' => [
        'Forex MMCIS Group' => [
            'url'      => 'https://ru.forex-mmcis.com/investment/?ref=87745',
            'refParam' => 'ref',
            'refId'    => '87745',
            'pammUrl'  => null,
            'pamms'    => null,
        ],
        'Panteon'           => [
            'url'      => 'http://panteon-finance.com/viewPage/viewinvestor/start737.html',
            'refParam' => 'invite',
            'refId'    => 'D8F98F',
            'pammUrl'  => 'http://panteon-finance.com/pammView.php?broker=panteon&invite=D8F98F&pamm={pamm}',
        ],
        'FX-Trend'          => [
            'url'      => 'http://fx-trend.com/landing/pamm1?agent=542907',
            'refParam' => 'agent',
            'refId'    => '542907',
            'pammUrl'  => 'http://fx-trend.com/pamm/{pamm}?agent=542907',
            'pamms'    => null,
        ],
        'Альпари'           => [
            'url'      => 'http://www.alpari.ru/ru/investor/pamm/investors/?partner_id=1221138',
            'refParam' => 'partner_id',
            'refId'    => '1221138',
            'pammUrl'  => 'http://www.alpari.ru/ru/investor/pamm/{pamm}/?partner_id=1221138',
            'pamms'    => [
                'Alpari Pamm Petrov_Ivan' => '172658',
            ],
        ],
        'FXOpen'            => [
            'url'      => 'http://www.fxopen.ru/Pamm/List.aspx?Culture=ru&agent=599791',
            'refParam' => 'agent',
            'refId'    => '599791',
            'pammUrl'  => 'http://www.fxopen.ru/Pamm/Account.aspx?Id={pamm}&Culture=ru&agent=599791',
            'pamms'    => [
                'FXOpen Pamm elrid(homeinvestblog)' => '4524e252-50ad-415b-9200-046a74be18c8',
                'FXOpen Pamm Trade-Bowl(ECNp20)'    => '7564ed40-30f5-460b-af93-f309c8d5e3c8',
                'SafePamm'                          => '6eaf30fb-e73e-46d0-84b2-33530e0f8150',
                'DmitriyECN(DmitriyECN)'            => 'b786db40-1671-4c50-a4a9-73cbce2b2d77',
            ],
        ],
        'Mill Trade'        => [
            'url'      => 'https://milltrade.net/?ref=52a3352aac928e3d08000005',
            'refParam' => 'ref',
            'refId'    => '52a3352aac928e3d08000005',
            'pammUrl'  => null,
            'pamms'    => null,
        ],
    ],
];

$config = array_merge($config, require 'local.php');
return $config;

