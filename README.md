<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

### Реализация тестового задания для Autodrive
### Автор: Газин Максим

#### Стоит обратить внимание [на](https://github.com/steel0rat/autodrive.docker/)
#### Ограничения:
- бд с поддержкой innoDb
- php: "^7.3|^8.0"

#### Запуск:
##### Eсли без докера, то:
- cp .env.example .env
- прописать параметры бд
- composer install
- php artisan migrate

### Куда смотреть:

#### database/migrations
- тут все миграции
- бд спроектирована с использованием связей и справочников
- в таблице car используется  композитный первичный ключ: связь car_id(id из xml) и generation_id
- я забыл добавить ограничение на уникальность связки car_id и generation_id
- схема бд:
<p><img src="http://gazinsmarthome.duckdns.org/db.png" alt="License"></p>

#### app/models
- тут все модельки из схемы

#### app/Http/Contollers
- EntityUpdater - контроллер, который добавляет, обновляет и удаляет сущности
- CarController - контроллер, который конфигурирует и передаёт данные в вышеупомянутый контроллер

#### app/Console/Commands/Car.php
- Контроллер консольной команды
- Запуск: php artisan car:import
- По умолчанию путь storage/app/car/data.xml
- В качестве параметра можно указать путь относительно корня приложения: php artisan car:import storage/app/data.xml
- Присутствует код, который чисто для красивого вывода в консоль, к заданию не имеет прямого отншения
- Использована транзакция на всякий пожарный

##### При запуске проверит:
- Существование файла
- То, что файл является xml
- Наличие offers
- Провалидирует офферы
- Выведет индексы невалидных оферов и причину невалидности
- Заполнит справочники по необходимости
- Передаст новые модели в контроллер, который добавит, обновит и удалит офферы
- Красиво выведет информацию
- Если изменений не было, то выведет ошибку

Пример Успешно выполненой команды:
<p><img src="http://gazinsmarthome.duckdns.org/good.png" alt="License"></p>















