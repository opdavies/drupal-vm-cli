<?php

namespace DrupalVmGenerator\Command;

use DrupalVmGenerator\Style\DrupalVmStyle;

trait ExtrasTrait
{
    public function extrasQuestion(DrupalVmStyle $io)
    {
        if ($io->confirm(
            'Do you want to install add any packages to installed_extras?',
            false
        )
        ) {
            $selectedExtras = [];
            $io->writeln(
                "\nType the package name from installed_extras or use keyup or keydown.\nThis is optional, press <info>enter</info> to <info>continue</info>.\n"
            );

            $extras = [
                'adminer',
                'drupalconsole',
                'mailhog',
                'memcached',
                'nodejs',
                'pimpmylog',
                'redis',
                'ruby',
                'selenium',
                'solr',
                'varnish',
                'xdebug',
                'xhprof',
            ];

            while (true) {
                $extra = $io->choiceNoList(
                    'Enter the name of the extra',
                    $extras,
                    null,
                    true
                );

                $extra = trim($extra);
                if (empty($extra)) {
                    break;
                }

                array_push($selectedExtras, $extra);
                $key = array_search($extra, $extras, true);

                if ($key >= 0) {
                    unset($extras[$key]);
                }
            }

            return $selectedExtras;
        }
    }
}
