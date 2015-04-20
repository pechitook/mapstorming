<?php namespace Mapstorming\ValidableQuestion;

use Mapstorming\ValidableQuestion\Rules\Rule;
use Mapstorming\ValidableQuestion\Rules\RuleFactory;
use Symfony\Component\Console\Question\Question;

class ValidableQuestion extends Question {

    private $rules;

    public function __construct($question, array $rules, $default = null) {
        parent::__construct($question, $default);

        $this->rules = $rules;
        $this->setValidator($this->validate());
    }

    private function validate() {
        $rules = $this->rules;

        return function ($answer) use ($rules) {
            foreach ($rules as $rule) {
                $ruleInstance = RuleFactory::makeFromRule($rule);
                $answer = $this->validateRule($ruleInstance, $answer);
            }

            return $answer;
        };
    }

    private function validateRule(Rule $rule, $answer) {
        $rule->validate($answer);

        return $answer;
    }
}