Installation
============

Before creating your first Marvic application, make sure that your local machine has [PHP](https://php.net/) and [Composer](https://getcomposer.org/) installed. If you don't have PHP and Composer installed in your machine, ...

If you already have PHP and Composer installed, you may install Marvic via Composer. create a directory to hold your application and create a new composer project.

```bash
$ mkdir myapp
$ cd myapp
$ composer init
```

These commands prompts you for a number of thing, such as the name, type and description of your project.

Now install Marvic in the `myapp` directory running the following command. Automatically, composer will save it in the dependences.

```bash
$ composer require musonacodex/marvic
```