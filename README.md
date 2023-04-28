## Установка зависимостей и настройка

### Значения по умолчанию:
- Логин и пароль mysql юзера - `dev` / `root`
- Название базы данных - `cscartex`
- Логин и пароль адиминистратора cscart - `dev@example.com` / `root`
- Адрес phpMyAdmin - `http://admin.devel/`
- Адрес cscart - `http://cscartex.devel/`
- Путь до папки с cscart - `~/apache_htdocs/public_html/cscartex.devel`

### Требуемые зависимости:
1. Apache2
2. MariaDB
3. PHP 7.4
4. CS-Cart v4.16.2_ru

### Установка необходимых зависимостей:
```sh
sudo apt install mariadb-server apache2
```
```sh
sudo add-apt-repository ppa:ondrej/php
```
```sh
sudo apt update
```
```sh
sudo apt install php7.4 php7.4-curl php7.4-xdebug \
php7.4-mysql php7.4-soap php7.4-zip \
php7.4-gd php7.4-xml php7.4-iconv \
php7.4-mbstring git
```

### Возможная ошибка

The following packages have unmet dependencies:
php8.2-gd : Depends: libgd3 (>= 2.3.3) but 2.3.0-2ubuntu2 is to be installed
E: Unable to correct problems, you have held broken packages.

### То сначала нужно вызывать:

1. `sudo apt install libgd3`
2. А затем установить PHP

### Настройка git
```sh
mkdir -p ~/apache_htdocs/public_html/cscartex.devel ; \
cd ~/apache_htdocs/public_html/cscartex.devel
```
- Склонировать текущий репозиторий в папку
- Затем:

```sh
mv cscart-exercise-1/* . ; \
mv cscart-exercise-1/.* . ; \
rmdir cscart-exercise-1
```

### Настройка mysql
```sh
sudo mysql
```

```mysql
CREATE USER 'dev'@'localhost' IDENTIFIED BY 'root';
```

```mysql
GRANT ALL PRIVILEGES ON *.* TO 'dev'@'localhost';
```

```mysql
FLUSH PRIVILEGES;
```

### Импорт базы данных 
```sh
mysql -u dev -p
```

```mysql
CREATE DATABASE cscartex;
```

```sh
unzip -p ~/apache_htdocs/public_html/cscartex.devel/var/backups/cscartex.sql.zip \
| mysql -u dev -p cscartex
```

### Настройка apache2
```sh
sudo cp readme/hosts /etc/ ; \
sudo cp readme/apache2.conf /etc/apache2 ; \
sudo cp readme/cscartex.devel.conf /etc/apache2/sites-available ; \
sudo cp readme/admin.devel.conf /etc/apache2/sites-available ; \
unzip readme/admin.devel.zip -d ~/apache_htdocs/public_html/ ; \
sudo sed -i "s/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=$USER/g" /etc/apache2/envvars ; \
sudo sed -i "s/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=$USER/g" /etc/apache2/envvars ; \
sudo a2ensite cscartex.devel.conf admin.devel.conf ; \
sudo a2enmod rewrite ; sudo systemctl restart apache2
```
- Попробовать открыть в браузере http://cscartex.devel и http://admin.devel

### Если не работает то:
```sh
sudo systemctl restart apache2 ; \
sudo chmod 775 -R ~/apache_htdocs ; \
sudo chown $USER -R ~/apache_htdocs
```
- После этого поидеи должно все заработать

## Тест-кейс

### Предусловия:
1. Есть тестовый магазин с установленной модификацией
2. Администратор, есть логин и пароль
3. Для администратора в панели администратора создано меню для управления отделами

### Тесты:
1. Тест панели администратора
2. Тест витрины

### Тест панели администратора: 
1. Зайти на главную страницу витрины магазина
2. Нажать "Мой профиль"
3. Нажать "Войти"
4. Ввести логин и пароль для администратора
5. Нажать "ВОЙТИ"
6. Нажать "Панель администратора"
7. Нажать "Покупатели"
8. Нажать "Отделы"
9. Нажать на кнопку с иконкой плюса
10. Заполнить поле "Название"
11. Заполнить поле "Руководитель"
12. Заполнить поле "Сотрудники" (минимум 2 сотрудника)
13. Нажать "Создать"
14. Проверить доступность тестового отдела на странице отделов
15. Нажать на название тестового отдела
16. Проверить поле "Название"
17. Проверить поле "Руководитель"
18. Проверить поле "Сотрудники"
19. Нажать на кнопку с иконкой шестеренки
20. Нажать на кнопку "Удалить"
21. Нажать на кнопку "OK"
22. Проверить доступность тестового отдела на странице отделов

### Ожидаемый результат:
- Тестовый отдел успешно создан
- Тестовый отдел отображается на странице отделов
- Страница тестового отдела открывается без ошибок
- Все поля тестового отдела соотвествуют полям при создании
- Тестовый отдел успешно удален
- Тестовый отдел не отображается на странице отделов

### Тест витрины:
1. Зайти на главную страницу витрины магазина
2. Нажать "Мой профиль"
3. Нажать "Войти"
4. Ввести логин и пароль для администратора
5. Нажать "ВОЙТИ"
6. Нажать "Мой профиль"
7. Нажать Отделы
8. Проверить, что тестовый отдел доступен на странице отделов
9. Нажать на название тестового отдела
10. Проверить, что страница тестового отдела доступна и открывается без ошибок
11. Проверить, что сотрудник доступен на странице отдела

### Ожидаемый результат:
- Тестовый отдел доступен на странице отделов
- Страница тестового отдела открывается без ошибок
- Сотрудники отображаются на странице тестового отдела

## Автотестирование
### Чтобы запустить автотесты:
1. Перейти в папку `var/tools/autotests`
2. В командной строке ввести ` php codecept.phar run --steps`
