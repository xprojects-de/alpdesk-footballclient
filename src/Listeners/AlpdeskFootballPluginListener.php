<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Listeners;

use Alpdesk\AlpdeskCore\Events\Event\AlpdeskCorePlugincallEvent;
use Alpdesk\AlpdeskCore\Library\Mandant\AlpdescCoreBaseMandantInfo;
use Twig\Environment;
use Contao\Environment as ContaoEnvironment;

class AlpdeskFootballPluginListener
{
    private Environment $twig;
    private string $projectDir;

    /**
     * AlpdeskFootballPluginListener constructor.
     * @param string $projectDir
     * @param Environment $twig
     */
    public function __construct(string $projectDir, Environment $twig)
    {
        $this->projectDir = $projectDir;
        $this->twig = $twig;
    }

    /**
     * @param AlpdescCoreBaseMandantInfo $mandantInfo
     * @throws \Exception
     */
    private function prepareMandant(AlpdescCoreBaseMandantInfo $mandantInfo)
    {
        $mandantdata = $mandantInfo->getAdditionalDatabaseInformation();

        $dbReference = (int)$mandantdata['footballmandantdb'];

        if ($dbReference === null || $dbReference <= 0) {
            throw new \Exception('invalid footballmandantdb');
        }

        //$connection = AlpdeskcoreDatabasemanagerModel::connectionById($dbReference);

        /*if ($connection === null) {
            throw new \Exception('invalid connection');
        }*/

    }

    /**
     * @param AlpdeskCorePlugincallEvent $event
     * @throws \Exception
     */
    public function __invoke(AlpdeskCorePlugincallEvent $event): void
    {
        try {

            $event->stopPropagation();

            $plugin = $event->getResultData()->getPlugin();
            $mandant = $event->getResultData()->getMandantInfo();
            $data = $event->getResultData()->getRequestData();

            $this->prepareMandant($mandant);

            $response = [
                'ngContent' => 'error loading data',
                'ngStylesheetUrl' => [
                    0 => ContaoEnvironment::get('base') . 'bundles/alpdeskautomationplugin/test.css'
                ],
                'ngScriptUrl' => [
                    0 => ContaoEnvironment::get('base') . 'bundles/alpdeskautomationplugin/test.js'
                ]
            ];

            switch ($plugin) {

                case 'football_members':
                {
                    $response['ngContent'] = 'Hello Members';
                    break;
                }

                case 'football_trainings':
                {
                    $response['ngContent'] = 'Hello Trainings';
                    break;
                }

                default:
                    break;
            }

            $event->getResultData()->setData($response);

        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
}
