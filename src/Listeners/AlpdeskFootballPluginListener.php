<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Listeners;

use Alpdesk\AlpdeskCore\Events\Event\AlpdeskCorePlugincallEvent;
use Alpdesk\AlpdeskCore\Library\Mandant\AlpdescCoreBaseMandantInfo;
use Alpdesk\AlpdeskCore\Model\Database\AlpdeskcoreDatabasemanagerModel;
use Alpdesk\AlpdeskFootball\Library\Members\Members;
use Alpdesk\AlpdeskFootball\Library\Trainings\Trainings;
use Alpdesk\AlpdeskFootball\Model\CrudModel;
use Twig\Environment;
use Contao\Environment as ContaoEnvironment;
use Contao\System;

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

        $connection = AlpdeskcoreDatabasemanagerModel::connectionById($dbReference);

        if ($connection === null) {
            throw new \Exception('invalid connection');
        }

        CrudModel::setConnection($connection);
        CrudModel::setMandantId($mandantInfo->getId());

    }

    /**
     * @param AlpdeskCorePlugincallEvent $event
     * @throws \Exception
     */
    public function __invoke(AlpdeskCorePlugincallEvent $event): void
    {
        try {

            if (!\in_array($event->getResultData()->getPlugin(), ['football_members', 'football_trainings'])) {
                return;
            }

            $event->stopPropagation();

            $plugin = $event->getResultData()->getPlugin();
            $mandant = $event->getResultData()->getMandantInfo();
            $data = $event->getResultData()->getRequestData();

            $this->prepareMandant($mandant);

            $response = [
                'ngContent' => 'error loading data',
                'ngStylesheetUrl' => [
                    0 => ContaoEnvironment::get('base') . 'bundles/alpdeskfootball/css/football_base.css?v=' . \time()
                ],
                'ngScriptUrl' => [
                    0 => ContaoEnvironment::get('base') . 'bundles/alpdeskfootball/js/football_base.js?v=' . \time()
                ]
            ];

            System::loadLanguageFile('default', 'de');

            switch ($plugin) {

                case 'football_members':
                {
                    $response['ngStylesheetUrl'][1] = ContaoEnvironment::get('base') . 'bundles/alpdeskfootball/css/members/football_members.css?v=' . \time();
                    $response['ngScriptUrl'][1] = ContaoEnvironment::get('base') . 'bundles/alpdeskfootball/js/members/football_members.js?v=' . \time();

                    $response['ngContent'] = (new Members($this->twig))->run();

                    break;
                }

                case 'football_trainings':
                {
                    $response['ngStylesheetUrl'][1] = ContaoEnvironment::get('base') . 'bundles/alpdeskfootball/css/trainings/football_trainings.css?v=' . \time();
                    $response['ngScriptUrl'][1] = ContaoEnvironment::get('base') . 'bundles/alpdeskfootball/js/trainings/football_trainings.js?v=' . \time();

                    $response['ngContent'] = (new Trainings($this->twig))->run();

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
