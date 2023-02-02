Usage
=============


Common use case:

.. code-block:: php

    namespace DTO;
    
    class CreateUserDTO
    {
        public string $email;
        public float $balance;
    }

.. code-block:: php

    $data = [
        'email' => 'test@mail.com',
        'balance' => 128.41,
    ];
    $dto = ClassTransformer::transform(CreateUserDTO::class, $data);
    var_dump($dto);

Output:

.. code-block:: bash
    object(\LoginDTO)
      'email' => string(13) "test@mail.com"
      'balance' => float(128.41)
