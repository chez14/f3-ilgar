# Ilgar 
[![PHP from Packagist](https://img.shields.io/packagist/php-v/chez14/f3-ilgar.svg?style=flat-square)](https://github.com/chez14/f3-ilgar)
 [![Travis (.org)](https://img.shields.io/travis/chez14/f3-ilgar.svg?style=flat-square)](https://github.com/chez14/f3-ilgar) [![Packagist](https://img.shields.io/packagist/v/chez14/f3-ilgar.svg?style=flat-square)](https://packagist.org/packages/chez14/f3-ilgar) [![GitHub release](https://img.shields.io/github/release/chez14/f3-ilgar.svg?style=flat-square)](https://github.com/chez14/f3-ilgar)

Quick and simple migration tool for Fat-Free Framework.


## Getting Started
1. Install via Composer
    ```bash
    composer require chez14/f3-ilgar
    ```

2. Decide your bombarding option
         
    - **Do it with default configuration?**
        Default settings:

        - Call `/ilgar/migrate` to do migration

        - Use `Migration` as your migration class namespace prefix

        - Migration packets placed in a `Migration` or `migration` folder in your project root folder.

        Then, just add this to your `index.php` file:
        ```php
        \Chez14\Ilgar\Boot::now();
        ```

    - **Do it with your own style and custom security?**

        Just invoke `\Chez14\Ilgar\Boot::trigger_on();` Anywhere at your controller. This will trigger migration process and returns [quick stats](#quick-stats).

3. Create your first ever migration packet

4. Deploy migration by accessing `/ilgar/migrate`
    ```bash
    curl http://localhost:8087/ilgar/migrate
    ```

    or

    ```bash
    php index.php /ilgar/migrate
    ```

## `MigrationPacket` Class Example

This file is available on your `migration` folder, located on your project root folder. Alternatively you can set the folder by setting `ILGAR.path`.

```php
$this->f3->set('ILGAR.path', "new-folder/");
```

<hr>

**IMPORTANT!** The file name should be formatted as "0-classname.php", where the 0 is any number (you can use just normal number `1-ClassName.php`, or CI-style timestamp `180901012400-ClassName.php`), seperated with single dash, and followed with your class name, either in lowercase, SnakeCase, or camelCase.

**IMPORTANT!** You need to **extends** `\Chez14\Ilgar\MigrationPacket` class. This will ensure required methods is always available and dependable.

<hr>

Here's your file: `1-test01.php`.

```php
namespace Migration;

class Test01 extends \Chez14\Ilgar\MigrationPacket {
    public function on_migrate(){
        // Do your things here!
        // All the F3 object were loaded, F3 routines executed,
        // this will just like you doing things in your controller file.
        
        $f3 = \F3::instance(); //get the $f3 from here.
        
        echo "Hello from Test01 Migration package";
    }

    public function on_failed(\Exception $e) {

    }
}
```



## `MigrationPacket` abstract class
It's just a normal class. With something that you need to implement:
 - `on_migrate()`
 - `on_failed(\Exception $e)`

Ilgar has 2 ability, *bomb* and *self-destruct*. **Bomb** means do the migration mission (`on_migrate`), **self-destruct** means when it failed do the mission it will use their remaining power to do suicide bombing, in this matter, it means rollback the migration mission (`on_failed`).

More convenient event-based functions (all of this is optional):
 - `pre_migrate()`

    Before the migration event. This might be useful if you want to prepare something first.

    Please check `is_migratable` if you want to wanted to do checks.

 - `post_migrate()`
   
    After migration event. This will be executed when the packet were successfully executed.

 - `is_migratable()` 

    Validates current packet if it is applicable. This must return true in order to make this packet executed.

## Quick Api for Ilgar
Ilgar's API is available on `Chez14\Ilgar\Internal` class. It's a `\Prefab` child class, do if you wanted to get it's instance, you can obtain the instance by calling `Chez14\Ilgar\Internal::instance()`. Aaanyway, here's the API list:

### get_current_version()
returns an `int`.

This integer represent current migration version point, declared by the filename.

Will load the version from `migration.json`.

### reset_version()
returns void.

Will forcefully delete `migration.json` at designated path.

### get_stats()

returns [quick stats](#quick-stats).

This will return current statistics with the migrations.

## Quick Stats

Quick stats is a array, consisting:

```php
$stats = [
    "success" => $counter, // how many migration executed
    "last_exception" => $failed, // last exception occured, this is an Exception object.
    "version" => $current // current version applied
];
```



## License
Yes, [GPLv3](LICENSE).

## FAQ and RAQ (Rarely Asked Question)

### Why naming it "Ilgar"?
[Ilgar](http://worldtrigger.wikia.com/wiki/Ilgar) is a bombarding-type Trion Warrior. It's used by [Aftocrator](http://worldtrigger.wikia.com/wiki/Aftokrator) and [Chion](http://worldtrigger.wikia.com/wiki/Chion) for invasion.
Yes, it's meant to bombarding the database with migration packets.
