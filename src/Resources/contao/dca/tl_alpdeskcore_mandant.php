<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;

PaletteManipulator::create()
    ->addField('footballmandantdb', 'default', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_alpdeskcore_mandant');

$GLOBALS['TL_DCA']['tl_alpdeskcore_mandant']['fields']['footballmandantdb'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_alpdeskcore_mandant']['footballmandantdb'],
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_alpdeskcore_databasemanager.title',
    'eval' => ['mandatory' => false, 'tl_class' => 'w50', 'includeBlankOption' => true],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];
