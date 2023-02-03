### " Ankara Büyükşehir Belediyesi Kar Takip " uygulamasındaki kameralı araçları m3u8 formatında oynatan süre limitine takılmadan izleyebileceğiniz örnek proje .

### Kurulum

````
cp .env.example .env

composer install

php artisan key:generate

php artisan migrate:fresh --seed

php artisan serve
````
