<?php

namespace Mapstorming\ValidableQuestion\Rules;


class NumericRule extends BaseRule {

    public function validate($answer) {
        if (!is_numeric($answer)) {
            $this->throwError('A numeric value is required.');
        }
    }
}