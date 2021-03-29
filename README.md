## Class-transformer helper

Class-transformer to transform your data into a typed object

## :scroll: **Installation**
The package can be installed via composer:
```
composer require yzen.dev/plain-to-class
```

## :scroll: **Usage**
> Alas, i do not speak English, and the documentation is compiled via google translate :( 

When writing code, it is very important to separate logic, adhere to the principle of single responsibility, reduce dependence on other services, and much more.
Therefore, I am trying to implement all business services through DTO.

This approach makes sure that the service will work with the data it needs,full typing, there will be no need to check the existence of keys if it is an array.

Suppose you have a service for authorization. To ensure the reliability of the input data, it would be good to send DTO there:
Let's say you have an authorization service. To ensure that the input is valid, it would be nice to send a DTO there. But then we will have to initiate and fill in each time ourselves, and you must admit that this is very inconvenient.
For this, this helper was created, which will allow you to easily create an instance of a DTO, or just any of your objects.
```php
class AuthController
{
    private AuthService $authService;
    
    //...
    
    public function register(RegisterUserRequest $request)
    {
        $registerDTO = ClassTransformer::transform(RegisterDTO::class, $request->toArray());
        $this->authService->register($registerDTO);
    }
```

If the data contains arguments that are not in the class, they will simply be skipped.

```php 
$data = [
    'email' => 'test',
    'password' => '123456',
    'fakeField' => 'fake',
];
$dto = ClassTransformer::transform(LoginDTO::class,$data);
var_dump($dto);
```
Result:
```php
object(\LoginDTO)[298]
  public string 'email' => string 'test' (length=4)
  public string 'password' => string '123456' (length=6)
```

If you need to implement your own transformation, for example, your input parameters have a different name for attributes, then you can implement the static of the transform method in the class.

## :scroll: **Recursive casting**

If you have an array of objects of a specific class, then you must specify the full path to the class in phpdoc `array <\ DTO \ ProductDTO>`.

This is done in order to know exactly which instance you need to create. Since Reflection does not provide out-of-the-box functionality to get the `use *` file. In addition to `use *`, an alias can be specified, and it will be more difficult to trace it.
Example:
```php
namespace DTO;

class ProductDTO
{
    public int $id;
    public string $name;
}
```
```php
namespace DTO;

class UserDTO
{
    public int $id;
    public string $email;
    public string $balance;
}
```

```php
class PurchaseDTO
{
    /** @var array<\DTO\ProductDTO> $products Product list */
    public array $products;
        
    /** @var \DTO\UserDTO $user */
    public UserDTO $user;
}
```

```php
$data = [
    'products' => [
        ['id' => 1, 'name' => 'phone',],
        ['id' => 2, 'name' => 'bread',],
    ],
    'user' => ['id' => 1, 'email' => 'test@test.com', 'balance' => 10012.23,],
];
$purcheseDTO = ClassTransformer::transform(PurchaseDTO::class, $data);
var_dump($purcheseDTO);
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
