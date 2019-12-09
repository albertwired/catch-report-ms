# Catch Report Microservice

Catch Report Microservice is a service to generate report from provided json file

## Installation

Clone This Repository.
```
git clone git@github.com:albertwired/catch-report-ms.git'
```

Create Environtment variables as defined on .env.example

```
cd catch-repot-ms
touch .env
```

Getting ready with depedencies

```
composer update
```
Generate the migration files
```
php bin/console make:migration
```

Migrationg the tables
```
php bin/console doctrine:migrations:migrate

```

Starting the server
```
php bin/console server:start
```

## Dependency
- ### Symfony 4.4
Core framework, why using version 4 instead of 5? This is my first time to purely use symfony and the documentations are mostly for version 4. There are symfony/amazon-mailer that helps me send email throug SES directly using main Symfony Mailer, symfony/filesystem to create attachment, symfony/orm-pack for databases operations.
- ### AWS-SDK
Used for S3 data read
- ### friendsofsymfony/rest-bundle
Used to render and serving RESTfull API and beautifully routes it.
- ### cerbero/json-objects
This one is awesome json file streamer, base on its documentation we can reduce memory usage while we read big json files.
- ### jms/serializer-bundle
This one brought alongside with FOS/rest-bundle to serialize and normailze the response, also acutally helps alot on file generation object normalization and serialization.
- ### nelmio/api-doc-bundle
I actually want to use l5-swagger instead of this, but the best practice and easy example that I got is this one. So I generate API Docs using this

## Tests
There are nothing yet and put it on To-do
## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.
## Author
Anggi Susanto

[rothdokenthir@gmail.com](mailto:rothdokenthir@gmail.com) | [antscpk06@gmail.com](mailto:antscpk06@gmail.com)


## License
[MIT](https://choosealicense.com/licenses/mit/)