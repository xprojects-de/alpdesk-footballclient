<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Listeners;

use Alpdesk\AlpdeskCore\Events\Event\AlpdeskCoreRegisterPlugin;

class AlpdeskFootballRegisterPluginListener
{
    public function __invoke(AlpdeskCoreRegisterPlugin $event): void
    {
        $data = $event->getPluginData();
        $info = $event->getPluginInfo();

        $data['football_members'] = $GLOBALS['TL_LANG']['ADME']['football_members'];
        $info['football_members'] = ['customTemplate' => true];

        $data['football_trainings'] = $GLOBALS['TL_LANG']['ADME']['football_trainings'];
        $info['football_trainings'] = ['customTemplate' => true];

        $event->setPluginData($data);
        $event->setPluginInfo($info);
    }
}
