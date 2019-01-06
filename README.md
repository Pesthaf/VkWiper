Вайпер исходящих сообщений в социальной сети VK
=====================
Скрипт предназначен для удаления отправленных сообщений спустя определенный интервал времени в т.ч. у собеседника.

Зависимости:
 - PHP >= 7.1;
 - [VK PHP-SDK](https://vk.com/dev/PHP_SDK);
 - php-curl;
 - Composer.

Использование
--------------------
Убедиться, что установленная версия PHP как минимум 7.1, иначе VK SDK не заведется. Установить php-curl (на примере Ubuntu с 7.2 версией PHP):
```bash
sudo apt install php7.2-curl
```
Установить Composer:
```bash
sudo apt install composer
```
Перейти в директорию с данным скриптом и с помощью Composer'а подключить к нему VK SDK:
```bash
composer require vkcom/vk-php-sdk
```
В случае успешного выполнения появятся файлы *composer.json composer.lock* и директория *vendor*.
В настройках скрипта (файл *config.php*) прописать access token (его получение выходит за рамки данного ридми, [см. здесь](https://readd.org/kak-poluchit-access_token-vkontakte/)) и идентификаторы диалогов для отслеживания (совпадают с ID собеседника).
По желанию прописать в cron автоматический запуск:
```bash
crontab -e
*/3 * * * * php -f /path/to/script/main.php >> /var/log/vk_wiper.log
```
Примечание
--------------------
Удалению подлежат сообщения, с отправки которых прошло как минимум *$interval* секунд, но не более 24 часов, т.к. VK не позволяет удалять старые сообщения у собеседников, и при этом только те, которые УЖЕ прочитаны собеседником.