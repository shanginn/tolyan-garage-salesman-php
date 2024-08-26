<?php

declare(strict_types=1);

namespace Tolyan\Tests;

use Faker\Factory;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected static \Faker\Generator $faker;
    public static function faker(): \Faker\Generator
    {
        if (!isset(self::$faker)) {
            self::$faker = Factory::create();
        }

        return self::$faker;
    }

}