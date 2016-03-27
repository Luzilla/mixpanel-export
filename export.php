#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Luzilla\ExportCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new ExportCommand());
$application->run();
