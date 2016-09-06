<?php

namespace DrupalVmGenerator\Command;

use DrupalVmGenerator\Style\DrupalVmStyle;

trait ExtrasTrait
{
    /**
     * A list of available extra packages.
     *
     * @var array
     */
    private static $extras = [
        'adminer',
        'blackfire',
        'drupalconsole',
        'elasticsearch',
        'mailhog',
        'memcached',
        'newrelic',
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

            $extras = self::$extras;

            while (true) {
                // Prompt the user to enter the name of the extra.
                $extra = $io->choiceNoList(
                    'Enter the name of the extra',
                    $extras,
                    null,
                    true
                );

                // Remove any whitespace and ensure that the input is not empty.
                $extra = trim($extra);
                if (empty($extra)) {
                    break;
                }

                // Push the item into the selected extras array.
                array_push($selectedExtras, $extra);

                // Remove the selected item from the available options.
                $key = array_search($extra, $extras, true);
                if ($key >= 0) {
                    unset($extras[$key]);

                    // Reset the array keys to avoid undefined offset errors.
                    $extras = array_values($extras);
                }
            }

            return $selectedExtras;
        }
    }
}
