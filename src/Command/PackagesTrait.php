<?php

namespace DrupalVm\Command;

use DrupalVm\Style\DrupalVmStyle;

trait PackagesTrait
{
    public function packagesQuestion(DrupalVmStyle $io)
    {
        if ($io->confirm(
            'Do you want to install add any additional packages?',
            false
        )
        ) {
            $extraPackages = [];
            // $io->writeln("\nType the package name from installed_extras or use keyup or keydown.\nThis is optional, press <info>enter</info> to <info>continue</info>.\n");

            while (true) {
                $package = $io->askEmpty(
                    'Enter the name of the package to install'
                );

                $package = trim($package);
                if (empty($package)) {
                    break;
                }

                array_push($extraPackages, $package);
            }

            return $extraPackages;
        }
    }
}
