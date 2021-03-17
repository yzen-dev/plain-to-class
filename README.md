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
