<?php

// Simple autoloader for Nananom Farms project
spl_autoload_register(function ($class) {
    // Convert namespace to directory path
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    
    // Check if the class uses our namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});