<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Library\Trainings;

use Alpdesk\AlpdeskFootball\Library\PluginBase;

class Trainings extends PluginBase
{
    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(): string
    {
        return $this->twig->render('@AlpdeskFootball/alpdeskfootball_trainings.html.twig', [
            'headline' => $GLOBALS['TL_LANG']['alpdeskfootball_trainings']['headline'],
            'reloadRole' => \json_encode([
                'type' => 'route',
                'target' => 'football_trainings',
                'params' => []
            ]),
            'reloadRole1' => \json_encode([
                'type' => 'route',
                'target' => 'football_members',
                'params' => []
            ]),
            'reload' => $GLOBALS['TL_LANG']['alpdeskfootball_reload']
        ]);
    }
}