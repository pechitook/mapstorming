<?php
namespace Mapstorming\Scrappers;


interface ScrapperInterface {

    public function scrap($cityId, $dataset, $input, $output);

} 