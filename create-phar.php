<?php

$filename = 'magedin';

try {
    $pharFile = __DIR__ . "/build/$filename.phar";

// clean up
    if (file_exists($pharFile)) {
        unlink($pharFile);
    }

    if (file_exists($pharFile . '.gz')) {
        unlink($pharFile . '.gz');
    }

// create phar
    $phar = new Phar($pharFile);

// start buffering. Mandatory to modify stub to add shebang
    $phar->startBuffering();

// Create the default stub from main.php entrypoint
    $defaultStub = $phar->createDefaultStub('main.php');

// Add the rest of the apps files
    $phar->buildFromDirectory(__DIR__ . '/src');

// Customize the stub to add the shebang
    $stub = "#!/usr/bin/env php \n" . $defaultStub;

// Add the stub
    $phar->setStub($stub);

    $phar->stopBuffering();

// plus - compressing it into gzip
    $phar->compressFiles(Phar::GZ);

# Make the file executable
    chmod($pharFile, 0770);

    echo "$pharFile successfully created" . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage();
}
