Laracasa - Laravel Picasa web album 
=========

This package is based on [Zend Gdata] which provides Google API service.

Installation
----

Update your `composer.json` file to include this package as a dependency
```json
"n0m4dz/laracasa": "dev-master"
```

Register the Laracasa service provider by adding it to the providers array in the `app/config/app.php` file.

```
'providers' => array(
    'N0m4dz\Laracasa\LaracasaServiceProvider'
)
```

Alias the Laracasa facade by adding it to the aliases array in the `app/config/app.php` file.
```php
'aliases' => array(
    'Laracasa' => 'N0m4dz\Laracasa\Facades\Laracasa'
)
```

# Configuration

Generate the config file into your project by running
```
php artisan config:publish n0m4dz/laracasa
```

This will generate a config file like this
```
return array(
    'user' => '',
    'password' => '',
    'album' => ''
);
```

After generated config file set values in return array. `user` = your gmail id, `password` = your gmail password, `album` = picasa web album ID.


# Usage

`getAlbum` function will retrieve all the photos from specific album.
```php

Laracasa::getAlbum();

```

`getPhotoById` function will retrieve a photo from specific album.
```php

Laracasa::getPhotoById($photo_id)

```

`addPhoto` function uploads a photo into album, then return uploaded photo ID.  
```php

Laracasa::addPhoto($_FILES['photo'])

```

`deletePhoto` function deletes a photo from album.  
```php

Laracasa::deletePhoto($photo_id);

```


[Zend Gdata]:http://framework.zend.com/manual/1.12/en/zend.gdata.html


    