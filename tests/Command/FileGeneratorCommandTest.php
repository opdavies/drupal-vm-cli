<?php

namespace DrupalVmGenerator\Tests\Command;

abstract class FileGeneratorCommandTest extends CommandTest
{
    /**
     * @var string
     */
    protected $filename;

     /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->deleteFile();
    }
}
