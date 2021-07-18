<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Listeners\Callbacks;

use Contao\DataContainer;

class DcaCallbacks
{
    /**
     * @param DataContainer $dc
     * @return array
     * @throws \Exception
     */
    public function getYouthes(DataContainer $dc): array
    {
        return [
            0 => '-',
            1 => 'F-Jugend'
        ];
    }
}
