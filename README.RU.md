## ClassTransformer

![Packagist Version](https://img.shields.io/packagist/v/yzen.dev/plain-to-class?color=blue&label=version)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/yzen-dev/plain-to-class/Run%20tests?label=tests&logo=github)
[![Coverage](https://codecov.io/gh/yzen-dev/plain-to-class/branch/master/graph/badge.svg?token=QAO8STLPMI)](https://codecov.io/gh/yzen-dev/plain-to-class)
![License](https://img.shields.io/github/license/yzen-dev/plain-to-class)
![Packagist Downloads](https://img.shields.io/packagist/dm/yzen.dev/plain-to-class)
![Packagist Downloads](https://img.shields.io/packagist/dt/yzen.dev/plain-to-class)


Эта библиотека позволит вам легко преобразовать любой набор данных в нужный вам объект. От вас не требуется менять структуру классов, наследовать их от внешних модулей и т.д. Никаких танцев с бубнами - только данные и нужный класс.

Хорошей практикой считается написание кода независимого от сторонних пакетов и фреймворков. Код разбивается на сервисы, доменные зоны, различные слои и т.д. Для передачи данных между слоями, как правило, используется шаблон DataTransfer Object (DTO). DTO - это объект, который необходим для инкапсуляции данных и отправки их из одной подсистемы приложения в другую.

Таким образом, сервисы/методы работают с конкретным объектом и данными необходимым для него. При этом неважно, откуда эти данные были получены - это может быть http запрос, БД, файл и т.д.

Соответственно, при каждом вызове сервиса нам необходимо инициализировать данное DTO. Но сопоставлять каждый раз данные вручную - неэффективно, и сказывается на читабельности кода, особенно если объект сложный.

Здесь на помощь приходит данный пакет, который берет на себя всю работу с мапингом и инициализацией необходимой DTO.

## :scroll: **Установка**

Пакет может быть установлен с помощью composer:

```
композитору требуется yzen.dev/plain-to-class
```

> Примечание: Текущая версия пакета поддерживает только PHP 8.1 +.

> Для PHP версии 7.4 вы можете ознакомиться с документацией
> в [версия v0.*](https://github.com/yzen-dev/plain-to-class/tree/php-7.4 ).
>
>Для PHP версии 8.0 вы можете ознакомиться с документацией
> в [версия v1.*](https://github.com/yzen-dev/plain-to-class/tree/php-8.0 ).

## :scroll: **Использование**

Общий вариант использования:

### :scroll: **Base**

```
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
$dto = ClassTransformer::transform(CreateUserDTO::class, $data);
var_dump($dto);
```

Result:

```php
object(\LoginDTO)
  'email' => string(13) "test@mail.com"
  'balance' => float(128.41) 
```

Также с версии php 8 вы можете передавать именованные аргументы:

```php 
$dto = ClassTransformer::transform(CreateUserDTO::class,
        email: 'test@mail.com',
        balance: 128.41
      );
```

Если свойство не является скалярным типом, и у него явно указан класс, оно будет автоматически рекурсивно к нему
приведено.

```php
class ProductDTO
{
    public int $id;
    public string $name;
}

class PurchaseDTO
{
    public ProductDTO $product;
    public float $cost;
}

$data = [
    'product' => ['id' => 1, 'name' => 'phone'],
    'cost' => 10012.23,
];

$purchaseDTO = ClassTransformer::transform(PurchaseDTO::class, $data);
var_dump($purchaseDTO);
```

Результат:

```php
object(PurchaseDTO)
  public ProductDTO 'product' => 
    object(ProductDTO)
      public int 'id' => int 1
      public string 'name' => string 'phone' (length=5)
  public float 'cost' => float 10012.23
```

### :scroll: **Коллекция**

Если у вас есть массив объектов определенного класса, то вы должны указать для него атрибут ConvertArray, передав ему в какой класс вам нужно привести элементы.

Также можно указать класс в PHP DOC, но тогда вам нужно написать полный путь к этому классу `array <\DTO\ProductDTO>`.
Это делается для того, чтобы точно знать, какой экземпляр нужно создать. Поскольку Reflection не предоставляет готовых
функций для получения файла `use`. Помимо `use`, вы можете указать псевдоним и его будет сложнее отследить. Пример:

```php

class ProductDTO
{
    public int $id;
    public string $name;
}

class PurchaseDTO
{
    #[ConvertArray(ProductDTO::class)]
    public array $products;
}

$data = [
    'products' => [
        ['id' => 1, 'name' => 'phone',],
        ['id' => 2, 'name' => 'bread',],
    ],
];
$purchaseDTO = ClassTransformer::transform(PurchaseDTO::class, $data);
```

#### :scroll: **Анонимная коллекция**

В случае если вам нужно преобразовать массив данных в массив объектов класса, вы можете реализовать это с помощью
метода `transformCollection`.

```php
$data = [
  ['id' => 1, 'name' => 'phone'],
  ['id' => 2, 'name' => 'bread'],
];
$products = ClassTransformer::transformCollection(ProductDTO::class, $data);
```

В результате этого вы получите массив объектов ProductDTO

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

Вам также может потребоваться поэлементное преобразование массива. В таком случае вы можете передать массив классов,
который затем можно легко распаковать

```php
    $userData = ['id' => 1, 'email' => 'test@test.com', 'balance' => 10012.23];
    $purchaseData = [
        'products' => [
            ['id' => 1, 'name' => 'phone',],
            ['id' => 2, 'name' => 'bread',],
        ],
        'user' => ['id' => 3, 'email' => 'fake@mail.com', 'balance' => 10012.23,],
    ];

    $result = ClassTransformer::transformMultiple([UserDTO::class, PurchaseDTO::class], [$userData, $purchaseData]);
    
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

### :scroll: ** Стиль написания**

Постоянная проблема со стилем написания, например, в базе данных это snake_case, а в коде camelCase.
И их постоянно нужно как-то трансформировать. Пакет позаботится об этом, вам просто нужно указать
атрибут WritingStyle в свойстве:

```php
class WritingStyleSnakeCaseDTO
{
    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE, WritingStyle::STYLE_SNAKE_CASE)]
    public string $contact_fio;

    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE)]
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

### :scroll: **Alias**

Для свойства можно задать различные возможные alias'ы, которые будут также искаться в источнике данных. Это может быть
полезно если DTO формируется по разным источникам данных.

```php
class WithAliasDTO
{
    #[FieldAlias('userFio')]
    public string $fio;

    #[FieldAlias(['email', 'phone'])]
    public string $contact;
}
```

### :scroll: **Кастомизация сеттеров**

Если поле требует дополнительной обработки при его инициализации, вы можете мутировать его сеттер. Для это создайте в
классе метод следующего формата -  `set{$name}Attribute`. Пример:

```php
class UserDTO
{
    public int $id;
    public string $real_address;

    public function setRealAddressAttribute(string $value)
    {
        $this->real_address = strtolower($value);
    }
}
```

### :scroll: **Пост обработка**

Внутри класса вы можете создать метод `afterTransform`, который вызовется сразу по завершению преобразования. В нем мы
можете описать свою дополнительную логику проверки или преобразования работая уже с состоянием объекта.

```php
class UserDTO
{
    public int $id;
    public float $balance;

    public function afterTransform()
    {
        $this->balance = 777;
    }
}
```

### :scroll: **Кастомное преобразование**

Если вам требуется полностью свое преобразование, то вы можете в классе создать метод transform. В таком случае никакие
обработки библиотеки не вызываются, вся ответственность преобразования переходит на ваш класс.

```php
class CustomTransformUserDTOArray
{
    public string $email;
    public string $username;
    
    public function transform($args)
    {
        $this->email = $args['login'];
        $this->username = $args['fio'];
    }
}
```
