<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addField('alpdeskfootball_youth', 'login_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToSubpalette('login', 'tl_member');

$GLOBALS['TL_DCA']['tl_member']['fields']['email']['eval']['unique'] = false;

$GLOBALS['TL_DCA']['tl_member']['fields']['alpdeskfootball_youth'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_member']['alpdeskfootball_youth'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'select',
    //'options_callback' => Done using contao.callback event
    'eval' => ['tl_class' => 'clr', 'multiple' => true, 'includeBlankOption' => true],
    'sql' => "blob NULL"
];
