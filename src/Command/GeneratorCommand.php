<?php

namespace DrupalVm\Command;

use DrupalVm\Exception\FileEmptyException;

abstract class GeneratorCommand extends Command
{
    /**
     * Check if the file already exists.
     *
     * @param string $filename The name of the file to check
     */
    protected function assertFileAlreadyExists($filename)
    {
        if (file_exists(
                $this->projectDir.'/'.$filename
            ) && !$this->input->getOption('overwrite')
        ) {
            $this->error("$filename already exists.");

            exit(1);
        }
    }

    /**
     * A shortcut for rendering a Twig template.
     *
     * @param $template
     * @param array $parameters
     *
     * @return string
     */
    protected function render($template, array $parameters)
    {
        return $this->container['twig']->render($template, $parameters);
    }

    /**
     * @param string $filename
     * @param string $contents
     *
     * @return Command
     *
     * @throws FileEmptyException
     */
    protected function writeFile($filename, $contents)
    {
        if (empty($contents)) {
            throw new FileEmptyException('The generated file was empty.');
        }

        $this->container['filesystem']->dumpFile($filename, $contents);

        $this->success(sprintf('%s created', $filename));

        return $this;
    }
}
