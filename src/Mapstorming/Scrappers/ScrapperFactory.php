<?php

namespace Mapstorming\Scrappers;


class ScrapperFactory {

    public static function getInstance($source)
    {
        $classname = '\\Mapstorming\\Scrappers\\' . ucwords($source) . 'Scrapper';
        $instance = new $classname;

        return $instance;
    }
}