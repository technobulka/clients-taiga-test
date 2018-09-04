# Clients App

Clients app is the test task for Taiga systems.

## Require

* Apache 2.4
* PHP 5.6
* MySQL 5.6

### Installation

Clone repository to yor local server.

```
git clone git://github.com/technobulka/clients-taiga-test.git
```

### Configuration

Fill the database connection fields in `/App/config/app.php`.

```
return [
    'db' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'taiga'
    ]
];
```

Create `clients` table in your database:

```
CREATE TABLE `clients` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `firstname` VARCHAR(50) NULL DEFAULT NULL,
    `lastname` VARCHAR(50) NULL DEFAULT NULL,
    `patronymic` VARCHAR(50) NULL DEFAULT NULL,
    `birthday` DATE NOT NULL,
    `sex` ENUM('-','M','F') NOT NULL DEFAULT '-',
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `edited` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
```

And `clients_phones` table:

```
CREATE TABLE `clients_phones` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `phone` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
```

## Built With

* [Bootstrap 4.0](https://getbootstrap.com/) - HTML + CSS library