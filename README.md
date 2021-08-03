# OC Projet 8 - TodoList

### 1. Install dependancies
```shell
composer install
```

### 2. create and configure the app
```shell
cp parameters.yml.dist parameters.yml
```

### 3. create the database table/schema
```shell
 php bin/console doctrine:schema:update --force
```

### 4. create a first user
```shell
INSERT INTO `user` (`id`, `username`, `password`, `email`, `roles`) VALUES (NULL, 'user_one', '###', 'admintest@test.com', '[\"ROLE_SUPER_ADMIN\"]');
```

### Run the tests
```shell
vendor/bin/phpunit
```

