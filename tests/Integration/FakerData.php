<?php

declare(strict_types=1);

namespace Tests\Integration;

trait FakerData
{
    public function getBaseArrayData(): array
    {
        return [
            'id' => 1,
            'email' => 'fake@mail.com',
            'balance' => 128.41,
            'isBlocked' => false
        ];
    }


    public function getArrayUsers(): array
    {
        return [
            ['id' => 1, 'email' => 'fake@mail.com', 'balance' => 128.41],
            ['id' => 1, 'email' => 'fake@mail.com', 'balance' => 128.41]
        ];
    }

    public function getRecursiveArrayData(): array
    {
        return [
            'products' => [
                ['id' => 1, 'name' => 'phone', 'price' => 43.03,],
                ['id' => 2, 'name' => 'bread', 'price' => 10.56,],
            ],
            'user' => ['id' => 1, 'email' => 'fake@mail.com', 'balance' => 10012.23,],
        ];
    }

    public function getTripleRecursiveArray(): array
    {
        return [
            'orders' => [
                [
                    'products' => [
                        ['id' => 47, 'name' => 'phone', 'price' => 43.03,],
                        ['id' => 56, 'name' => 'bread', 'price' => 10.56,],
                    ],
                    'user' => ['id' => 1, 'email' => 'fake@mail.com', 'balance' => 10012.23,],
                ],
                [
                    'products' => [
                        ['id' => 73, 'name' => 'laptop', 'price' => 1200.00,],
                        ['id' => 32, 'name' => 'tomato', 'price' => 10.56,],
                    ],
                    'user' => ['id' => 2, 'email' => 'fake2@mail.com', 'balance' => 6731.38,],
                ]
            ]
        ];
    }

    public function getBaseObject(): \stdClass
    {
        $data = new \stdClass();
        $data->id = 1;
        $data->email = 'fake@mail.com';
        $data->balance = 128.43;
        $data->isBlocked = false;
        return $data;
    }

    public function getRecursiveObject(): \stdClass
    {
        $productOne = new \stdClass();
        $productOne->id = 1;
        $productOne->name = 'phone';
        $productOne->price = 43.03;
        $productTwo = new \stdClass();
        $productTwo->id = 2;
        $productTwo->name = 'bread';
        $productTwo->price = 10.56;
        $user = new \stdClass();
        $user->id = 1;
        $user->email = 'fake@mail.com';
        $user->balance = 10012.23;
        $data = new \stdClass();
        $data->products = [$productOne, $productTwo];
        $data->user = $user;
        return $data;
    }

    public function getTripleRecursiveObject(): \stdClass
    {
        $productOne = new \stdClass();
        $productOne->id = 47;
        $productOne->name = 'phone';
        $productOne->price = 43.03;
        $productTwo = new \stdClass();
        $productTwo->id = 2;
        $productTwo->name = 'bread';
        $productTwo->price = 10.56;
        $user = new \stdClass();
        $user->id = 1;
        $user->email = 'fake@mail.com';
        $user->balance = 612.23;
        $orderOne = new \stdClass();
        $orderOne->products = [$productOne, $productTwo];
        $orderOne->user = $user;
        $productOne = new \stdClass();
        $productOne->id = 73;
        $productOne->name = 'laptop';
        $productOne->price = 1200.00;
        $productTwo = new \stdClass();
        $productTwo->id = 32;
        $productTwo->name = 'tomato';
        $productTwo->price = 10.56;
        $user = new \stdClass();
        $user->id = 2;
        $user->email = 'fake2@mail.com';
        $user->balance = 10012.23;
        $orderTwo = new \stdClass();
        $orderTwo->products = [$productOne, $productTwo];
        $orderTwo->user = $user;
        $data = new \stdClass();
        $data->orders = [$orderOne, $orderTwo];
        return $data;
    }
}
