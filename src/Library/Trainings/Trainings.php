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
            'form' => \json_encode([
                'type' => 'form',
                'target' => 'football_trainings',
                'fields' => [
                    [
                        'type' => 'text',
                        'value' => 'Hello World',
                        'placeHolder' => 'Insert Value',
                        'label' => 'Name',
                        'name' => 'name',
                        'mandatory' => true,
                        'class' => 'w50'
                    ],
                    [
                        'type' => 'text',
                        'value' => 'Hello Description',
                        'placeHolder' => 'Insert Value',
                        'label' => 'Description',
                        'name' => 'description',
                        'class' => 'clr'
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'type' => 'checkbox',
                        'value' => true,
                        'label' => 'MyCheckbox',
                        'name' => 'mycheckbox'
                    ],
                    [
                        'type' => 'date',
                        'value' => '2021-01-01',
                        'placeHolder' => 'Insert Value',
                        'label' => 'Datum',
                        'name' => 'date',
                        'mandatory' => true,
                        'class' => 'w50'
                    ],
                    [
                        'type' => 'date',
                        'value' => '2021-01-02',
                        'placeHolder' => 'Insert Value',
                        'label' => 'Datum2',
                        'name' => 'date2',
                        'class' => 'w50'
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'type' => 'radio',
                        'value' => 'Value2',
                        'placeHolder' => '',
                        'label' => 'MyRadio',
                        'name' => 'myradio',
                        'items' => [
                            [
                                'label' => 'Label1',
                                'value' => 'Value1'
                            ],
                            [
                                'label' => 'Label2',
                                'value' => 'Value2'
                            ]
                        ]
                    ],
                    [
                        'type' => 'select',
                        'value' => 'Value4',
                        'placeHolder' => '',
                        'label' => 'MySelect',
                        'name' => 'myselect',
                        'items' => [
                            [
                                'label' => 'Label3',
                                'value' => 'Value3'
                            ],
                            [
                                'label' => 'Label4',
                                'value' => 'Value4'
                            ]
                        ]
                    ],
                    [
                        'type' => 'multiSelect',
                        'value' => ['Value6'],
                        'placeHolder' => '',
                        'label' => 'MyMultiSelect',
                        'name' => 'mymultiselect',
                        'items' => [
                            [
                                'label' => 'Label5',
                                'value' => 'Value5'
                            ],
                            [
                                'label' => 'Label6',
                                'value' => 'Value6'
                            ]
                        ]
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'type' => 'submit',
                        'label' => 'weg!',
                        'name' => 'submitButton'
                    ],
                ]
            ]),
            'reload' => $GLOBALS['TL_LANG']['alpdeskfootball_reload']
        ]);
    }
}