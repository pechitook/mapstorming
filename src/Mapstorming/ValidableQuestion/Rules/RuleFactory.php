<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 12/18/14
 * Time: 18:53
 */

namespace Mapstorming\ValidableQuestion\Rules;


class RuleFactory {

    public static function makeFromRule($rule) {
        if ($rule == 'required') return new RequiredRule();
        if ($rule == 'numeric') return new NumericRule();
    }
} 