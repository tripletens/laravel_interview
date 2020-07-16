<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Products;
use Faker\Generator as Faker;
use App\Categories;

$factory->define(Products::class, function (Faker $faker) {
    return [
        //
        'name' => $faker->name,
        'description' => $faker->text,
        'category_id' => factory(Categories::class),
    ];
});
