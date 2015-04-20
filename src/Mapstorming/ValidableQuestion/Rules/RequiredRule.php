<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 12/18/14
 * Time: 18:54
 */

namespace Mapstorming\ValidableQuestion\Rules;


class RequiredRule extends BaseRule {

    public function validate($answer) {
        if (!$answer){
            $this->throwError('This is required.');
        }
    }
}