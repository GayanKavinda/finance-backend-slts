<?php
if (function_exists('shell_exec')) {
    echo "shell_exec is enabled\n";
    $output = shell_exec('echo hello');
    echo "Output: " . $output;
} else {
    echo "shell_exec is disabled";
}
