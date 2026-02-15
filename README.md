# REST API генерации случайных чисел

REST API для генерации и получения случайных чисел. Написан на чистом PHP с использованием DDD и модульной архитектуры.

## Архитектура

Проект построен на **модульной слоёной архитектуре**:

- **Kernel** — ядро: HTTP-абстракция, роутинг, DI-контейнер, система модулей
- **Module** — самодостаточные бизнес-модули, каждый со своими слоями Domain, Application, Infrastructure, Presentation
- **Client** — клиентская библиотека на основе CURL для работы с API

## Требования

- PHP >= 8.1
- Composer (для автозагрузки)
- Расширение CURL (для клиентской библиотеки)

## Установка

```bash
composer install
```

## Запуск сервера

```bash
php -S localhost:8080 -t public/
```

## API-методы

### Генерация случайного числа

```
GET /api/random
```

Ответ:
```json
{"id": "67af1234abcde", "number": 42}
```

### Получение числа по ID

```
GET /api/get?id=67af1234abcde
```

Ответ:
```json
{"id": "67af1234abcde", "number": 42}
```

## OpenAPI-спецификация

Спецификация API находится в файле:

```text
src/Module/RandomNumber/docs/openapi.yaml
```

Открыть и проверить спецификацию можно в Swagger Editor:

```text
https://editor.swagger.io/
```

## Запуск демо-клиента

В отдельном терминале (при запущенном сервере):

```bash
php demo.php
```

## Структура проекта

```
src/
├── Kernel/              # Ядро фреймворка
│   ├── Http/            # Request, Response, Router
│   ├── Module/          # ModuleInterface
│   ├── Container/       # DI-контейнер
│   └── Application.php  # Загрузка модулей и обработка запросов
└── Module/
    └── RandomNumber/    # Бизнес-модуль
        ├── Domain/      # Сущности, Value Objects, интерфейс репозитория
        ├── Application/ # Use Cases, DTO
        ├── Infrastructure/ # Реализация репозитория
        └── Presentation/   # Контроллеры
client/                  # Клиентская библиотека на CURL
```
