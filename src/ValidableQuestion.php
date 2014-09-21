<?php namespace Mapstorming;

use Symfony\Component\Console\Question\Question;

class ValidableQuestion extends Question{

    private $rules;

    public function __construct($question, array $rules, $default = null)
    {
        parent::__construct($question, $default);

        $this->rules = $rules;
        $this->setValidator($this->validate());
    }

    private function validate()
    {
        $rules = $this->rules;

        return function ($answer) use ($rules) {
            foreach ($rules as $rule){
                $answer = $this->validateRule($rule, $answer);
            }
            return $answer;
        };
    }

    private function validateRule($rule, $answer)
    {
        if ($rule == 'required'){
            if (!$answer) {
                throw new \RuntimeException(
                    'Come on, give me something!'
                );
            }
        }

        if ($rule == 'numeric'){
            if (!is_numeric($answer)) {
                throw new \RuntimeException(
                    'Please, enter a num3r1c value'
                );
            }
        }

        return $answer;
    }
}