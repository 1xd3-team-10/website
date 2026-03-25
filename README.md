# 1XD3-Team-10

User Table
```
CREATE TABLE `Team10`.`users` (`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `username` TEXT NOT NULL , `password_hash` TEXT NOT NULL , `password_salt` TEXT NOT NULL , `full_name` TEXT NOT NULL , `email` TEXT NOT NULL , `created_at` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`user_id`), UNIQUE (`username`), UNIQUE (`email`)) ENGINE = InnoDB COMMENT = 'table to store user data';```
