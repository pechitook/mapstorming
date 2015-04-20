<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 12/18/14
 * Time: 19:02
 */

namespace Mapstorming\ValidableQuestion\Rules;


abstract class BaseRule implements Rule {
    public $error;

    public function throwError($message){
        throw new \RuntimeException($message);
    }
} 