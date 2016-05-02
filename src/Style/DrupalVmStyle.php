<?php

namespace DrupalVmGenerator\Style;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;
use DrupalVmGenerator\Helper\DrupalVmChoiceQuestionHelper;

class DrupalVmStyle extends SymfonyStyle
{
    private $input;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;

        parent::__construct($input, $output);
    }

    /**
     * @param string $question
     * @param array  $choices
     * @param null   $default
     * @param bool   $allowEmpty
     *
     * @return string
     */
    public function choiceNoList($question, array $choices, $default = null, $allowEmpty = false)
    {
        if ($allowEmpty) {
            $default = ' ';
        }

        if (is_null($default)) {
            $default = current($choices);
        }

        if (!in_array($default, $choices)) {
            $choices[] = $default;
        }

        if (null !== $default) {
            $values = array_flip($choices);
            $default = $values[$default];
        }

        return trim($this->askChoiceQuestion(new ChoiceQuestion($question, $choices, $default)));
    }

    /**
     * @param ChoiceQuestion $question
     *
     * @return string
     */
    public function askChoiceQuestion(ChoiceQuestion $question)
    {
        $questionHelper = new DrupalVmChoiceQuestionHelper();
        return $questionHelper->ask($this->input, $this, $question);
    }

    public function askEmpty($question, $validator = null)
    {
        $question = new Question($question, ' ');
        $question->setValidator($validator);

        return trim($this->askQuestion($question));
    }
}
