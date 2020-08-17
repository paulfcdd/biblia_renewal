#### Развертывание системы при помощи Docker


###### Требования к системе
Для установки необходимо иметь установленные Docker(`v. 19.03`) и Docker-compose (`v. 1.24` или выше)


###### Шаги по установке
- склонировать репозиторий
- перейти в папку с проектом и выполнить в терминале `docker-compose up -d --build sf_app`
- загрузить дамп базы данных при помощи команды `docker-compose exec -T mariadb mysql -uroot -pmysql %your_db_name% < _old_site/db_dump.sql`
- после того как сборка контейнеров завершится в терминале нужно ввести команду `docker-compose exec sf_app bash` чтобы
попасть внутрь контейнера и потом выполнить команду `composer install`


###### После установки 
После установки так же рекомендуется выполнить следующие шаги
- создание пользователя с правами администратора. Для этого в терминале поочередно выполнить следующие команды: 
`docker-compose exec sf_app bash` и `bin/console app:admin:create user_email@example.com user_passord`
