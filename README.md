## About Project

This is a realtime message translation web app created as a academic project using Vue, Laravel and Tailwind (VLT) stack.

## How to set up

Clone the project and open it in terminal, copy .env.example file, rename to .env and set your DB configuration, then, run the comands:
```
composer install

php artisan key:generate
php artisan migrate --seed

npm install
npm run build
```

now, yo can set up the server with:
```
php artisan serve
```


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
