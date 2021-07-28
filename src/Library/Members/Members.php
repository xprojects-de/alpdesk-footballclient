<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Library\Members;

use Alpdesk\AlpdeskFootball\Library\PluginBase;

class Members extends PluginBase
{
    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(): string
    {
        return $this->twig->render('@AlpdeskFootball/alpdeskfootball_members.html.twig', [
            'headline' => $GLOBALS['TL_LANG']['alpdeskfootball_members']['headline'],
            'reload' => $GLOBALS['TL_LANG']['alpdeskfootball_reload'],
            'reloadRole' => \json_encode([
                'type' => 'route',
                'target' => 'football_members',
                'params' => []
            ]),
            'list' => \json_encode([
                'type' => 'list',
                'items' => [
                    [
                        'headline' => 'hallo',
                        'subHeadline' => 'welt',
                        'lines' => [
                            'Bla Bla',
                            '<strong>Bla Bla i</strong>',
                            'Bla Bla 2'
                        ],
                        'buttons' => [
                            [
                                'label' => 'bearbeiten',
                                'target' => 'football_members',
                                'params' => [
                                    'hallo' => 'Welt'
                                ]
                            ],
                            [
                                'label' => 'löschen'
                            ]
                        ]
                    ],
                    [
                        'headline' => 'hallo1',
                        'subHeadline' => '',
                        'buttons' => [
                            [
                                'label' => 'bearbeiten'
                            ],
                            [
                                'label' => 'löschen'
                            ]
                        ]
                    ]
                ]
            ]),
            'members' => $this->getMembersList()
        ]);
    }

    /**
     * @return array
     */
    private function getMembersList(): array
    {
        return [
            [
                'active' => true,
                'Lastname' => 'Hummel',
                'Firstname' => 'Niklas',
                'Street' => 'Auf der Halde 1',
                'Postal' => '87534',
                'City' => 'Oberstaufen',
                'Country' => 'Deutschland',
                'Phone1' => '08325',
                'Phone2' => '',
                'Email' => 'infos@x-projects.de',
                'Comment' => '',
                'Youth' => ['F']
            ],
            [
                'active' => true,
                'Lastname' => 'Hummel',
                'Firstname' => 'Elias',
                'Street' => 'Auf der Halde 9',
                'Postal' => '87534',
                'City' => 'Oberstaufen',
                'Country' => 'Deutschland',
                'Phone1' => '08325',
                'Phone2' => '0162',
                'Email' => 'bench@x-projects.de',
                'Comment' => 'Ich bin ein Kommentar und möchte noch was dazu sagen.<br> Was auch immer!!!',
                'Youth' => ['G', 'F']
            ]
        ];

    }

}