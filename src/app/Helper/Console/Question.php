<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Helper\Console;

use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question as ConsoleQuestion;

class Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $question
     * @param bool $default
     * @return bool
     */
    public function confirm(InputInterface $input, OutputInterface $output, string $question, bool $default): bool
    {
        if (true === $default) {
            $question .= " (default: Yes)";
        }

        $confirmation = ObjectManager::getInstance()->create(ConfirmationQuestion::class, [
            'question' => "<question>$question</question>",
            'default' => $default,
        ]);
        return $this->getDialog()->ask($input, $output, $confirmation);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $question
     * @param string|bool $default
     * @return string
     */
    public function ask(InputInterface $input, OutputInterface $output, string $question, $default): string
    {
        if (!empty($default)) {
            $question .= " (default: $default)";
        }

        $questionObject = ObjectManager::getInstance()->create(ConsoleQuestion::class, [
            'question' => "<question>$question</question>",
            'default' => $default
        ]);
        return $this->getDialog()->ask($input, $output, $questionObject);
    }

    /**
     * @return QuestionHelper
     */
    private function getDialog(): QuestionHelper
    {
        return ObjectManager::getInstance()->create(QuestionHelper::class);
    }
}
