<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 12/18/14
 * Time: 18:49
 */

namespace Mapstorming\ValidableQuestion\Rules;


interface Rule {
    public function validate($answer);
} 