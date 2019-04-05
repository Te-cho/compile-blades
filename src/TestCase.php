<?php
asd@a
use Mockery as m;
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
}
