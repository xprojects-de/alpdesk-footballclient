<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Library;

use Twig\Environment;

abstract class PluginBase
{
    protected Environment $twig;

    /**
     * PluginBase constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }


    abstract public function run(): string;

}