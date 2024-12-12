# PHP ETL: Import and Update Estate Data

## Описание проекта

Этот проект реализует ETL (Extract, Transform, Load) процесс для обработки данных из Excel файлов и загрузки этих данных в базу данных MySQL. В рамках проекта создается структура для обработки и обновления данных недвижимости, включая агентства, контакты, менеджеров и объявления, с последующим экспортом этих данных в формат XML фидов.

### Цель проекта:
- Импортировать данные из Excel файлов в базу данных.
- Обновлять существующие записи на основе новых данных из Excel.
- Создать REST API для получения данных в формате XML фидов.
- Собрать и запустить проект в Docker контейнерах.

### Принципы и паттерны:
- **SOLID** принципы объектно-ориентированного программирования.
- Использование паттерна **Фабрика** для создания объектов импортеров.
- Разделение ответственности и **Single Responsibility Principle** для классов и методов.

## Структура проекта

```
/php_etl_project 
├── /data 
│ ├── mysql 
│ │ └── dump.sql 
│ └── estate.xlsx 
│ └── estate-update.xlsx 
├── /public 
│ ├── api.php 
│ ├── import.php 
│ ├── import-data.php 
├── /src 
│ ├── Database
│ │ └── Connection.php 
├── /docker
│   ├── Dockerfile
│   ├── nginx.conf
│ ├── Models
│ │ └── Agency.php 
│ │ ├── BaseModel.php 
│ │ ├── Contact.php 
│ │ ├── Estate.php 
│ │ ├── Manager.php 
│ ├── Services 
│ │ ├── ExcelImporter.php 
│ │ └── FeedGenerator.php 
│ └── /Utils 
│ │ └── Logger.php 
├── docker-compose.yml 
├── .env 
└── README.md
```

### Описание структуры:

- **/public** - Содержит публичные файлы, такие как `api.php`, `import.php` и `import-data.php`.
- **/data** - Хранит Excel файлы, такие как `estate.xlsx` и дамп SQL.
- **/src/Services** - Сервисы для импорта данных, обновления и генерации фидов.
- **/src/Models** - Модели, представляющие сущности базы данных: агентства, менеджеры, контакты, объявления.
- **/src/Utils** - Утилиты, например, для логирования.
- **/docker** - Конфигурации Docker, включая `nginx.conf` для работы с Nginx.
- **docker-compose.yml** - Конфигурация для сборки и запуска контейнеров.

## Установка и запуск

1. Клонируйте репозиторий:

```bash
git clone git@github.com:errand/php-etl.git
cd php-etl
```

2. Создайте файл .env в корне проекта.
Заполните тестовыми кредами
   
   ```
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=real_estate
   DB_USERNAME=user
   DB_PASSWORD=password
   ```

3. Соберите и запустите контейнеры:
```docker-compose up --build```

4. Пример работы скрипта:

Можно запустить первичный импорт

```http://localhost/import.php```

Можно зайти в контейнер и запустить скрипт обновления

```
docker ps
docker exec -it <php-fpm-container-id> bash 
php import-data.php estate_update.xlsx
```


## API
Эндпоинты:
- GET /api/agencies - Получить список всех агентств.
- GET /api/contacts - Получить список всех контактов с возможностью фильтрации по агентству.
- GET /api/contacts?agency_id - Получить список всех контактов c фильтром.
- GET /api/managers - Получить список всех менеджеров с возможностью фильтрации по агентству.
- GET /api/managers?agency_id=2 - Получить список всех менеджеров  с фильтром по agency_id
- GET /api/estates - Получить список всех объявлений с фильтрацией по агентству, контакту, менеджеру.
- GET /api/estates?agency_id=2&contact_id=3 - Получить список объявлений с фильтром по параметрам

Формат ответа: XML.

## Принципы разработки
- SOLID 
- Singleton Connection для управления подключением к базе данных.
- Паттерн Фабрика используется для создания объектов импортера, который абстрагирует процесс создания объектов для обработки данных.

## Автор 
Александр Шацких

work@errand.ru