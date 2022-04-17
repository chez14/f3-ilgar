# Ilgar 
[![PHP from
 Packagist](https://img.shields.io/packagist/php-v/chez14/f3-ilgar.svg?style=flat-square)](https://github.com/chez14/f3-ilgar)
 [![Travis
 (.org)](https://img.shields.io/travis/chez14/f3-ilgar.svg?style=flat-square)](https://github.com/chez14/f3-ilgar)
 [![Packagist](https://img.shields.io/packagist/v/chez14/f3-ilgar.svg?style=flat-square)](https://packagist.org/packages/chez14/f3-ilgar)
 [![GitHub
 release](https://img.shields.io/github/release/chez14/f3-ilgar.svg?style=flat-square)](https://github.com/chez14/f3-ilgar)

Migration Library for Fat-Free Framework.

# Quick Links

- Documentation: <br/>
  *tbd*.

- Packagist Page: <br/>
  <https://packagist.org/packages/chez14/f3-ilgar>

- GitLab CI Reports:<br/>
  <https://gitlab.com/chez14/f3-ilgar/-/pipelines>

# Getting Started
1. Install via Composer
    ```bash
    composer require chez14/f3-ilgar
    ```

2. Decide your bombarding option
         
    - **Do it with default configuration?** Default settings:

        - Call `/ilgar/migrate` to do migration

        - Use `Migration` as your migration class namespace prefix

        - Migration packets placed in a `Migration` or `migration` folder in
          your project root folder.

        Then, just add this to your `index.php` file:

        ```php
        \CHEZ14\Ilgar\Boot::now();
        ```

    - **Do it with your own style and custom security?**

        Just invoke `\CHEZ14\Ilgar\Boot::trigger_on();` Anywhere at your
        controller. This will trigger migration sequence.

3. Create your first ever migration packet

4. Deploy migration by accessing `/ilgar/migrate`
    ```bash
    curl http://localhost/ilgar/migrate
    ```

    or

    ```bash
    php index.php /ilgar/migrate
    ```

# `Migration` Class Example

This file is available on your `migration` folder, located on your project root
folder. Alternatively you can set the folder by setting `ILGAR.path` via FatFree registry.

```php
$this->f3->set('ILGAR.path', "new-folder/");
```

<hr>

**IMPORTANT!** The file name should be formatted as "0-classname.php", where the
0 is any number (you can use just normal number `1-ClassName.php`, or
CodeIgniter-style timestamp `180901012400-ClassName.php`), seperated with single
dash, and followed with your class name, either in lowercase, SnakeCase, or
camelCase.

**IMPORTANT!** You need to **extends** `\CHEZ14\Ilgar\Migration` class. This
will ensure required methods is always available and dependable.

<hr>

Here's your file: `1-test01.php`.

```php
namespace Migration;

class Test01 extends \CHEZ14\Ilgar\Migration {
    public function onMigrate() : void{
        // Do your things here!
        // All the F3 object were loaded, F3 routines executed,
        // this will just like you doing things in your controller file.
        
        $f3 = \F3::instance(); //get the $f3 from here.
        
        echo "Hello from Test01 Migration package";
    }

    public function onFailed(\Exception $e) : void {

    }
}
```

# `Migration` abstract class

// TODO: Update this docs.

It's just a normal class. With something that you need to implement:
 - `up()`
 - `down(?\Exception $e)`

Ilgar has 2 ability, *bomb* and *self-destruct*. **Bomb** means do the migration
mission (`on_migrate`), **self-destruct** means when it failed do the mission it
will use their remaining power to do suicide bombing, in this matter, it means
rollback the migration mission (`on_failed`).

More convenient event-based functions (all of this is optional):
 - `pre_migrate()`

    Before the migration event. This might be useful if you want to prepare
    something first.

    Please check `is_migratable` if you want to wanted to do checks.

 - `post_migrate()`
   
    After migration event. This will be executed when the packet were
    successfully executed.

 - `is_migratable()` 

    Validates current packet if it is applicable. This must return true in order
    to make this packet executed.



# License
Yes, [GPLv3](LICENSE).

# FAQ and RAQ (Rarely Asked Question)

## Why naming it "Ilgar"?
[Ilgar](http://worldtrigger.wikia.com/wiki/Ilgar) is a bombarding-type Trion
Warrior. It's used by
[Aftocrator](http://worldtrigger.wikia.com/wiki/Aftokrator) and
[Chion](http://worldtrigger.wikia.com/wiki/Chion) for invasion. Yes, it's meant
to bombarding the database with migration packets.
