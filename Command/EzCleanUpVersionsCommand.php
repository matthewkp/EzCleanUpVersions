<?php

namespace Matthewkp\EzCleanUpVersionsBundle\Command;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Bridge\Monolog\Logger;

class EzCleanUpVersionsCommand extends Command
{
    /** @var \eZ\Publish\API\Repository\Repository */
    protected $repository;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /** @var \Symfony\Bridge\Monolog\Logger */
    protected $logger;

    /** @var int */
    protected $rootLocationId;

    /** @var int */
    protected $adminId;

    /** @var int */
    protected $numberOfVersionsToKeep;

    public function __construct(
        Repository $repository,
        LocationService $locationService,
        ContentService $contentService,
        SearchService $searchService,
        UserService $userService,
        Logger $logger,
        $rootLocationId,
        $adminId,
        $numberOfVersionsToKeep
    )
    {
        $this->repository = $repository;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->searchService = $searchService;
        $this->userService = $userService;
        $this->logger = $logger;
        $this->rootLocationId = $rootLocationId;
        $this->adminId = $adminId;
        $this->numberOfVersionsToKeep = $numberOfVersionsToKeep;

        parent::__construct();
    }

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('matthewkp:ez-clean-up-versions')
            ->setDescription('This script will remove old versions of all contents')
            ->addOption('keep', null, InputOption::VALUE_OPTIONAL, 'How many versions to keep?', false)
            ->addOption('locationId', null, InputOption::VALUE_OPTIONAL, 'Which location id?', false)
            ->addOption('adminId', null, InputOption::VALUE_OPTIONAL, 'Which admin user id?', false);
    }

    /**
     * Execute script
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start removing versions from contents");

        if (false !== $input->getOption('keep')) {
            $this->numberOfVersionsToKeep = $input->getOption('keep');
        }
        if (false !== $input->getOption('locationId')) {
            $this->rootLocationId = $input->getOption('locationId');
        }
        if (false !== $input->getOption('adminId')) {
            $this->adminId = $input->getOption('adminId');
        }

        if ($output->isVerbose()) {
            $output->writeln('Number of versions to keep: ' . $this->numberOfVersionsToKeep);
            $output->writeln('Root location id: ' . $this->rootLocationId);
            $output->writeln('Admin user id: ' . $this->adminId);
        }

        $permissionResolver = $this->repository->getPermissionResolver();

        $adminUser = $this->userService->loadUser($this->adminId);
        $permissionResolver->setCurrentUserReference($adminUser);

        $location = $this->locationService->loadLocation($this->rootLocationId);
        $this->browseLocation($location, 0, $output);

        $output->writeln("End");
    }

    /**
     * Clean ups versions for a content Id given
     *
     * @param $contentId
     */
    private function cleanUpVersions($contentId, $output)
    {
        $content = $this->contentService->loadContent($contentId);

        $contentVersions = $this->contentService->loadVersions($content->contentInfo);
        if ($output->isVerbose()) {
            $output->writeln('Content id ' . $contentId . ' has ' . count($contentVersions) . ' versions.');
        }

        $versionsToRemove = count($contentVersions) - $this->numberOfVersionsToKeep;
        if ($output->isVerbose()) {
            $output->writeln($versionsToRemove . ' versions will be removed.');
        }

        if ($versionsToRemove) {
            $i = 0;
            foreach ($contentVersions as $contentVersion) {
                if ($i < $versionsToRemove) {
                    try {
                        if ($output->isVerbose()) {
                            $output->writeln('Removing version ' . $contentVersion->contentInfo->currentVersionNo);
                        }
                        $this->contentService->deleteVersion($contentVersion);
                    } catch (\Exception $e) {
                        $this->logger->error('Exception threw for content Id ' .  $contentId . ' and version id ' . $contentVersion->contentInfo->currentVersionNo . '. Message : ' . $e->getMessage());
                    }
                } else {
                    break;
                }
                $i++;
            }
        }
    }

    /**
     * This function browse location children recursively
     *
     * @param Location $location
     * @param int $depth
     */
    private function browseLocation(Location $location, $depth = 0, $output)
    {
        $this->cleanUpVersions($location->contentId, $output);

        $childLocations = $this->locationService->loadLocationChildren($location);
        foreach ($childLocations->locations as $childLocation) {
            $this->browseLocation($childLocation, $depth + 1, $output);
        }
    }
}
