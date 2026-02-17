Установка и запуск 
Клонируйте репозиторий:

    git clone https://github.com/Bexruz999/task_manager
    cd task_manager
Настройте окружение:

    cp .env.example .env
    # Укажите PAYMENT_SECRET_KEY в .env для проверки Webhook
    # sudo apt install php8.4-xmlwriter
    composer install

Запустите Docker-контейнеры:

    docker-compose up -d
    docker exec -it task_manager_app php artisan migrate

    Запустите воркер очередей:
    Bash

    docker exec -it task_manager_app php artisan queue:work

Тестирование

    docker exec -it task_manager_app php artisan test


Laravel Horizon
http://127.0.0.1:8000/horizon

Mailpit
http://127.0.0.1:8025/
