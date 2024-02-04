

<h3 align="center">ESGI Project</h3>
<p align="center">Un site de gestion de projet développé avec Symfony</p>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li><a href="#built-with">Built With</a></li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#feature">Features</a></li>
  </ol>
</details>

### Built With
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)
[![Bootstrap][Bootstrap.com]][Bootstrap-url]

## Getting Started
### Prerequisites

* composer : https://getcomposer.org/download/
  
* php >= 8.1.* : https://www.php.net/downloads

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/antoinebtn/esgi_project
   ```
2. Install dependencies
   ```sh
   composer install
   ```
3. Configure the database in `.env`
   ```env
   DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
   ```

4. Configure the stripe api key in `.env`
   ```env
   STRIPE_SECRET_KEY="!ChangeMe!"
   ```
   
5. Create the database, migrate the migrations and load the fixtures 
   ```sh
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   symfony console doctrine:fixtures:load
   ```

## Features

Use this space to show useful examples of how a project can be used. Additional screenshots, code examples and demos work well in this space. You may also link to more resources.

### Integration stripe

![img_1.png](docs/img_1.png)

### Workflow Bundle

Utilisation du bundle Workflow de symfony pour la gestion des statuts des différents tickets

![img.png](docs/img.png)

### WYSIWYG Editor

Intégration de : https://github.com/eckinox/tinymce-bundle pour l'édition des tickets


<!-- MARKDOWN LINKS & IMAGES -->
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
