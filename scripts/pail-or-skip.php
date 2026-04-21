<?php

if (function_exists('pcntl_fork')) {
    passthru('php artisan pail --timeout=0', $exitCode);

    exit($exitCode);
}

fwrite(STDOUT, "Skipping pail because pcntl is unavailable.\n");

while (true) {
    sleep(1);
}