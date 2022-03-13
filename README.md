## Class-transformer helper

![Packagist Version](https://img.shields.io/packagist/v/yzen.dev/plain-to-class?color=blue&label=version)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/yzen-dev/plain-to-class/Run%20tests/php-7.4?label=tests&logo=github)
[![Coverage](https://codecov.io/gh/yzen-dev/plain-to-class/branch/php-7.4/graph/badge.svg?token=QAO8STLPMI)](https://codecov.io/gh/yzen-dev/plain-to-class)
![License](https://img.shields.io/github/license/yzen-dev/plain-to-class)
![Packagist Downloads](https://img.shields.io/packagist/dm/yzen.dev/plain-to-class)
![Packagist Downloads](https://img.shields.io/packagist/dt/yzen.dev/plain-to-class)

> Alas, I do not speak English, and the documentation was compiled through google translator :(
> I will be glad if you can help me describe the documentation more correctly :)

This package will help you transform any dataset into a structured object. This is very convenient when values obtained
from a query, database, or any other place can be easily cast to the object you need. But what exactly is this
convenient?

When writing code, it is very important to separate logic, adhere to the principle of single responsibility, reduce
dependence on other services, and much more.

When creating a new service to create a user, you only need the necessary data set - name, email and phone. Why do you
need to check around separately arrays, separately objects, check for the presence of keys through isset. It is much
more convenient to make a DTO model with which the service will already work.

This approach guarantees that the service will work with the data it needs, full typing, there is no need to check for
the presence of keys if it is an array.

## :scroll: **Installation**

The package can be installed via composer:

```
composer require yzen.dev/plain-to-class
```

>This branch contains PHP 7.4 support

## :scroll: **Usage**

Common use case:

### :scroll: **Base**

```php
namespace DTO;

class CreateUserDTO
{
    public string $email;
    public float $balance;
}
```

```php 
$data = [
    'email' => 'test@mail.com',
    'balance' => 128.41,
];
$dto = ClassTransformer::transform(CreateUserDTO::class,$data);
var_dump($dto);
```

Result:

```php
object(\LoginDTO)
  'email' => string(13) "test@mail.com"
  'balance' => float(128.41) 
```

Also for php 8 you can pass named arguments:

```php 
$dto = ClassTransformer::transform(CreateUserDTO::class,
        email: 'test@mail.com',
        balance: 128.41
      );
```

If the property is not of a scalar type, but a class of another DTO is allowed, it will also be automatically converted.

### :scroll: **Collection**

If you have an array of objects of a certain class, then you must specify the ConvertArray attribute to it, passing it
to which class you need to cast the elements.

It is also possible to specify the class in PHP DOC, but then you need to write the full path to this
class `array <\ DTO \ ProductDTO>`. This is done in order to know exactly which instance you need to create. Since
Reflection does not provide out-of-the-box functions for getting the `use *` file. Besides `use *`, you can specify an
alias, and it will be more difficult to trace it. Example:

```php

class ProductDTO
{
    public int $id;
    public string $name;
}


class UserDTO
{
    public int $id;
    public string $email;
    public string $balance;
}


class PurchaseDTO
{
    /** @var array<\DTO\ProductDTO> $products Product list */
    public array $products;
        
    /** @var UserDTO $user */
    public UserDTO $user;
}

$data = [
    'products' => [
        ['id' => 1, 'name' => 'phone',],
        ['id' => 2, 'name' => 'bread',],
    ],
    'user' => ['id' => 1, 'email' => 'test@test.com', 'balance' => 10012.23,],
];
$purchaseDTO = ClassTransformer::transform(PurchaseDTO::class, $data);
var_dump($purchaseDTO);
```

```php
object(PurchaseDTO)[345]
  public array 'products' => 
    array (size=2)
      0 => 
        object(ProductDTO)[1558]
          public int 'id' => int 1
          public string 'name' => string 'phone' (length=5)
      1 => 
        object(ProductDTO)[1563]
          public int 'id' => int 2
          public string 'name' => string 'bread' (length=5)
  public UserDTO 'user' => 
    object(UserDTO)[1559]
      public int 'id' => int 1
      public string 'email' => string 'test@test.com' (length=13)
      public float 'balance' => float 10012.23
```

### :scroll: **Anonymous array**

In case you need to convert an array of data into an array of class objects, you can implement this through an anonymous
array. To do this, you just need to wrap the class in **[]**

```php
$data = [
        ['id' => 1, 'name' => 'phone'],
        ['id' => 2, 'name' => 'bread'],
    ];
$products = ClassTransformer::transform([ProductDTO::class], $data);
```

The result of this execution you will get an array of objects ProductDTO

```php
array(2) {
  [0]=>
      object(ProductDTO) {
        ["id"]=> int(1)
        ["name"]=> string(5) "phone"
      }
  [1]=>
      object(ProductDTO) {
        ["id"]=> int(2)
        ["name"]=> string(5) "bread"
      }
} 
```

You may also need an element-by-element array transformation. In such a case, you can pass an array of classes, which
can then be easily unpacked

```php
    $userData = ['id' => 1, 'email' => 'test@test.com', 'balance' => 10012.23];
    $purchaseData = [
        'products' => [
            ['id' => 1, 'name' => 'phone',],
            ['id' => 2, 'name' => 'bread',],
        ],
        'user' => ['id' => 3, 'email' => 'fake@mail.com', 'balance' => 10012.23,],
    ];

    $result = ClassTransformer::transform([UserDTO::class, PurchaseDTO::class], [$userData, $purchaseData]);
    
    [$user, $purchase] = $result;
    var_dump($user);
    var_dump($purchase);
```

Result:

```php
object(UserDTO) (3) {
  ["id"] => int(1)
  ["email"]=> string(13) "test@test.com"
  ["balance"]=> float(10012.23)
}

object(PurchaseDTO) (2) {
  ["products"]=>
  array(2) {
    [0]=>
    object(ProductDTO)#349 (3) {
      ["id"]=> int(1)
      ["name"]=> string(5) "phone"
    }
    [1]=>
    object(ProductDTO)#348 (3) {
      ["id"]=> int(2)
      ["name"]=> string(5) "bread"
    }
  }
  ["user"]=>
  object(UserDTO)#332 (3) {
    ["id"]=> int(3)
    ["email"]=> string(13) "fake@mail.com"
    ["balance"]=> float(10012.23)
  }
}
```

### :scroll: **Writing style**

A constant problem with the style of writing, for example, in the database it is snake_case, and in the camelCase code.
And they constantly need to be transformed somehow. The package takes care of this, you just need to specify the
WritingStyle attribute on the property:

```php
class WritingStyleSnakeCaseDTO
{
    /**
     * @var string $contact_fio 
     * @writingStyle<WritingStyle::STYLE_SNAKE_CASE|WritingStyle::STYLE_CAMEL_CASE>
     */
    public string $contact_fio;

    /**
     * @var string $contact_fio 
     * @writingStyle<WritingStyle::STYLE_CAMEL_CASE>
     */
    public string $contact_email;
}


 $data = [
            'contactFio' => 'yzen.dev',
            'contactEmail' => 'test@mail.com',
        ];
$model = ClassTransformer::transform(WritingStyleSnakeCaseDTO::class, $data);
var_dump($model);
```

```php
RESULT:

object(WritingStyleSnakeCaseDTO) (2) {
  ["contact_fio"]=> string(8) "yzen.dev"
  ["contact_email"]=> string(13) "test@mail.com"
}
```
