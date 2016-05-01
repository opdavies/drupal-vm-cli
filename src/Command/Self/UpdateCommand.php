<?php

namespace DrupalVMGenerator\Command\Self;

use DrupalVmGenerator\Command\Command;
use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Herrera\Phar\Update\Update;
use Herrera\Version\Parser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{

    const MANIFEST_FILE = 'https://www.drupalvmgenerator.com/manifest.json';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('self:update')
            ->setDescription('Update drupalvm.phar to the latest version')
            ->setAliases(['update']);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $manifest = Manifest::loadFile(self::MANIFEST_FILE);

        $currentVersion = Parser::toVersion($this->getApplication()->getVersion());

        $update = $manifest->findRecent($currentVersion, true);

        if (false === $update instanceof Update) {
            $this->io->writeln(sprintf('You are already using the latest version: <info>%s</info>', $currentVersion));
            return 0;
        }

        $this->io->writeln(sprintf('Updating to version <info>%s</info>', $update->getVersion()));

        $manager = new Manager($manifest);
        $manager->update($this->getApplication()->getVersion(), true);

        $this->io->writeln(sprintf('SHA1 verified <info>%s</info>', $update->getSha1()));
    }

}
