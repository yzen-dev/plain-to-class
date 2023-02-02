ClassTransformer
================

This library will allow you to easily convert any data set into the object you need. You are not required to change the structure of classes, inherit them from external modules, etc. No dancing with tambourines - just data and the right class.

It is considered good practice to write code independent of third-party packages and frameworks. The code is divided into services, domain zones, various layers, etc.

To transfer data between layers, the **DataTransfer Object** (DTO) template is usually used. A DTO is an object that is used to encapsulate data and send it from one application subsystem to another.

Thus, services/methods work with a specific object and the data necessary for it. At the same time, it does not matter where this data was obtained from, it can be an http request, a database, a file, etc.

Accordingly, each time the service is called, we need to initialize this DTO. But it is not effective to compare data manually each time, and it affects the readability of the code, especially if the object is complex.

This is where this package comes to the rescue, which takes care of all the work with mapping and initialization of the necessary DTO.


.. toctree::
   :maxdepth: 2

   installation
   usage
