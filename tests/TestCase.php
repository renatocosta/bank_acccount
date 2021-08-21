<?php


namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $faker;

    protected function faker(): Generator
    {
        return $this->faker = $this->faker ?: Factory::create();
    }
}