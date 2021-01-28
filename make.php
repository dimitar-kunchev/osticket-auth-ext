<?php 

ini_set('phar.readonly',0);

$phar = new Phar('auth-ext.phar');
$phar->startBuffering();
$phar->buildFromIterator(new ArrayIterator([
    'authenticate.php' => __DIR__.'/authenticate.php', 
    'config.php' => __DIR__.'/config.php', 
    'plugin.php' => __DIR__.'/plugin.php'
]));
$phar->setStub('<?php __HALT_COMPILER();');
$phar->stopBuffering();