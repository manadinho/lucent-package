<?php
namespace Manadinho\Lucent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Lucent
 * @package Manadinho\Lucent\Facades\Lucent
 * 
 * @author Muhammad Imran Israr (mimranisrar6@gmail.com)
 */
class Lucent extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string The service container binding key for your Lucent class.
     */
    protected static function getFacadeAccessor()
    {
        return 'Lucent';
    }
}
