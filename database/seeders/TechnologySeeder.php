<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Technology::factory()->count(10)->create();

        //Technology::query()->insert([
        $technologies = [
            [
                'slug' => 'laravel',
                'name' => 'Laravel',
                'description' => 'Laravel is a web application framework with expressive, elegant syntax.',
                'image' => null,
                'link' => 'https://laravel.com',
            ],
            [
                'slug' => 'react',
                'name' => 'React',
                'description' => 'A JavaScript library for building user interfaces.',
                'image' => null,
                'link' => 'https://reactjs.org',
            ],
            [
                'slug' => 'vue',
                'name' => 'Vue',
                'description' => 'The Progressive JavaScript Framework.',
                'image' => null,
                'link' => 'https://vuejs.org',
            ],
            [
                'slug' => 'angular',
                'name' => 'Angular',
                'description' => 'One framework. Mobile & desktop.',
                'image' => null,
                'link' => 'https://angular.io',
            ],
            [
                'slug' => 'php',
                'name' => 'PHP',
                'description' => 'PHP is a popular general-purpose scripting language that is especially suited to web development.',
                'image' => null,
                'link' => 'https://www.php.net',
            ],
            [
                'slug' => 'javascript',
                'name' => 'JavaScript',
                'description' => 'JavaScript is an object-oriented computer programming language commonly used to create interactive effects within web browsers.',
                'image' => null,
                'link' => 'https://www.javascript.com',
            ],
            [
                'slug' => 'jave',
                'name' => 'Java',
                'description' => 'Java is a class-based, object-oriented programming language.',
                'image' => null,
                'link' => 'https://www.java.com',
            ],
            [
                'slug' => 'spring',
                'name' => 'Spring',
                'description' => 'Spring is a framework that provides comprehensive infrastructure support for developing Java applications.',
                'image' => null,
                'link' => 'https://spring.io',
            ],
            [
                'slug' => 'javafx',
                'name' => 'JavaFX',
                'description' => 'JavaFX is a software platform for creating and delivering desktop applications that can run across a wide variety of devices.',
                'image' => null,
                'link' => 'https://openjfx.io',
            ],
            [
                'slug' => 'c#',
                'name' => 'C#',
                'description' => 'C# is a programming language developed by Microsoft that is used for building a wide variety of applications.',
                'image' => null,
                'link' => 'https://docs.microsoft.com/en-us/dotnet/csharp',
            ],
            [
                'slug' => 'dotnet',
                'name' => '.NET',
                'description' => '.NET is a free, cross-platform, open source developer platform for building many different types of applications.',
                'image' => null,
                'link' => 'https://dotnet.microsoft.com',
            ],
            [
                'slug' => 'mysql',
                'name' => 'MySQL',
                'description' => 'MySQL is a database management system.',
                'image' => null,
                'link' => 'https://www.mysql.com',
            ],
            [
                'slug' => 'postgresql',
                'name' => 'PostgreSQL',
                'description' => 'PostgreSQL is a powerful, open source object-relational database system.',
                'image' => null,
                'link' => 'https://www.postgresql.org',
            ],
            [
                'slug' => 'mongodb',
                'name' => 'MongoDB',
                'description' => 'MongoDB is a source-available cross-platform document-oriented database program.',
                'image' => null,
                'link' => 'https://www.mongodb.com',
            ],
            [
                'slug' => 'sqlite',
                'name' => 'SQLite',
                'description' => 'SQLite is a software library that implements a self-contained, serverless, zero-configuration, transactional SQL database engine.',
                'image' => null,
                'link' => 'https://www.sqlite.org',
            ],
        ];
        collect($technologies)->each(function ($technology) {
            Technology::query()->create($technology);
        });
    }
}
