# Ilgar
Quick and simple migration tool for Fat-Free Framework.


## Getting Started
1. Install via Composer
    ```bash
    composer require chez14/f3-ilgar
    ```

2. Decide your bombarding option
        
    - Do it with default configuration?
        - call `/ilgar/migrate` to do migration

        - use `Migration` as your migration class namespace prefix
        
        - Migration packets placed in a `Migration` or `migration` folder in your root folder.

        Add this to your `index.php` file:
        ```php
        \Chez14\Ilgar\Boot::now();
        ```

    - Do it with your own style and custom security?
        - Read more here.

4. Create your first ever migration packet

5. Deploy migration by accessing `/ilgar/migrate`
    ```bash
    curl http://localhost:8087/ilgar/migrate
    ```

    or

    ```bash
    php index.php /ilgar/migrate
    ```

## MigrationPacket Class Example
**TBA**

## MigrationPacket abstract class
It's just a normal class. With something that you need to implement:
 - `on_migrate()`
 - `on_falied(\Exception $e)`

 Ilgar has 2 ability, *bomb* and *self-destruct*. **Bomb** means do the migration mission (`on_migrate`), **self-destruct** means when it failed do the mission it will use their remaining power to do suicide bombing, in this matter, it means rollback the migration mission (`on_failed`).

More convenient event-based functions (all of this is optional):
 - `pre_migrate()`

    Before the migration event. This might be usefull if you want to prepare something first.

    Please check `is_migratable` if you want to wanted to do checks.

 - `post_migrate()`
    
    After migration event. This will be executed when the packet were succesfully executed.

 - `is_migratable()` 
 
    Validates current packet if it is aplicable. This must return true in order to make this packet executed.
 
## Quick Api for Ilgar
Ilgar's API is available on `Chez14\Ilgar\Internal` class. It's a `\Prefab` child class, do if you wanted to get it's instance, you can obtain the instance by calling `Chez14\Ilgar\Internal::instance()`. Aaanyway, here's the API list:

### get_current_version()
returns an `int`.

This integer represent current migration version point, declared by the filename.

Will load the version from `migration.json`.

### reset_version()
returns void.

Will forcefully delete `migration.json` at designated path.

## License
Yes, [GPLv3](LICENSE).

## FAQ and RAQ (Rarely Asked Question)

### Why naming it "Ilgar"?
[Ilgar](http://worldtrigger.wikia.com/wiki/Ilgar) is a bombarding-type Trion Warrior. It's used by [Aftocrator](http://worldtrigger.wikia.com/wiki/Aftokrator) and [Chion](http://worldtrigger.wikia.com/wiki/Chion) for invasion.
Yes, it's meant to bombarding the database with migration packets.