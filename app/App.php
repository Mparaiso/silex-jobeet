<?php

/**
 * Job board application
 * @author M.Paraiso <mparaiso@online.fr>
 */
class App extends \Silex\Application
{
    function __construct(array $values=array()){
        parent::__construct($values);
        $this->register(new Config);
    }
}