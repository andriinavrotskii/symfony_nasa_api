<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\ExternalApi\Nasa\NasaException;
use AppBundle\Service\NasaDataUpdater;



class UpdateDataFromNasa extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('nasa:update')
            ->setDescription('Update data in DB from NASA API');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $nasa = $this->getContainer()->get('nasa_api');
            $nasaDataUpdater = $this->getContainer()->get('nasa_data_updater');

            $data = $nasa->getNeo();

            $result = $nasaDataUpdater->update($data);
            $output->writeln(
                "Imported {$result['all']} NEOs."
                . " {$result['new']} of them is new"
            );

        } catch (NasaException $e) {
            $output->writeln($e->getMessage());
        }
    }
}