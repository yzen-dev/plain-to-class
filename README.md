## Class-transformer function plainToClass

Class-transformer function to transform our object into a typed object

## :scroll: **Installation**
The package can be installed via composer:
```
composer require yzen.dev/plain-to-class
```

## :scroll: **Usage**
Suppose you have a service for authorization. To ensure the reliability of the input data, it would be good to send DTO there:
```php
class AuthService {
    public function register(RegisterDTO $data)
    {
        //crete user...
    }
}
```
```php
class RegisterDTO
{
    /** @var string Email */
    public string $email;
    
    /** @var string Password */
    public string $password;
}
```
But then we will have to create an object of this DTO each time:
```php
    public function register(RegisterUserRequest $request)
    {
        $registerDTO = new RegisterDTO();
        $registerDTO->email= $request->email;
        $registerDTO->password= $request->password;
        $this->authService->register($registerDTO);
    }
```
For this, this method was created, which will allow you to create an instance DTO without much difficulty.
```php
class AuthController
{
    private AuthService $authService;
    
    //...
    
    public function register(RegisterUserRequest $request)
    {
        $registerDTO = plainToClass(RegisterDTO::class, $request->toArray());
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
$dto = plainToClass(LoginDTO::class,$data);
var_dump($dto);
```
Result:
```php
object(\LoginDTO)[298]
  public string 'email' => string 'test' (length=4)
  public string 'password' => string '123456' (length=6)
```

## :scroll: **Recursive casting**

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
If you have a DTO array, then you must specify the full path to the class in phpdoc `array<\DTO\ProductDTO>`.

This is done in order to know exactly which instance you need to create. Because Reflection does not provide out-of-the-box functionality for getting a `use *`. In addition to the `use *`, an alias can be prescribed, and this will be more difficult to track.
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
$purcheseDTO = plainToClass(PurchaseDTO::class, $data);
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
