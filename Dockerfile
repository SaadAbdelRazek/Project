FROM php:8.2-cli

# تثبيت المتطلبات
RUN apt-get update && apt-get install -y \
    git unzip curl zip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مجلد العمل
WORKDIR /app

# نسخ ملفات المشروع
COPY . .

# تثبيت مكتبات Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# إعطاء الصلاحيات للمجلدات المطلوبة
RUN chmod -R 777 storage bootstrap/cache

# تشغيل السيرفر
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
