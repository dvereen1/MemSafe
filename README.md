# MemSafe

Never worry about forgetting with MemSafe.

Store an encrypted "memory" note and supply a secret used to unlock the memory later.

[Demo here](https://darianvereen.com/HTML_PHP/projects/memSafe/memSafe.php)

## Prerequisites

  * **Apache 2.4** or later - https://httpd.apache.org/
  * **PHP 7.2** or later - https://www.php.net/
  * **MySQL 8.0** or later - https://dev.mysql.com/doc/

  * **Libsodium**, or **Sodium**, a cryptographic library. Sodium comes packaged with PHP versions 7.2 and later but requires an extension installation for 7.0 and earlier versions.
  More info: https://www.php.net/manual/en/sodium.installation.php

## Database

### Memory Table

| Field    | Type         | Null | Key | Default | Extra          |
|----------|--------------|------|-----|---------|----------------|
| mem_id   | int          | No   | PRI | NULL    | auto_increment |
| uptime   | datetime     | No   |     | NULL    |                |
| username | varchar(255) | No   | UNI | NULL    |                |
| secret   | varchar(255) | No   |     | NULL    |                |
| note     | text         | Yes  |     | NULL    |                |

## Includes and Other Links

In [memSafe.php](https://github.com/dvereen1/MemSafe/blob/main/memSafe.php), you'll notice some file includes and references which exist outside the current directory.

```
<?php include_once("headNoNav.php");?>
<link rel = "stylesheet" href = "/CSS/allProjectsModal.css">
<?php include_once("Classes/projectInfoModal.php");
       createProjectModal("MemSafe", $projectModalArr);
?>

...

<script src = "/JS/allProjectsModal.js"></script>
<script type = "text/javascript" src ="../JSClasses/formValidator.js"></script>
```
You can view these files at [PHP-JS-CSS-Includes](https://github.com/dvereen1/PHP-JS-CSS-Includes).