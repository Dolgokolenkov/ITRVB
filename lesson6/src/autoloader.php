<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    function my_autoloader($class) {
        
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $class = str_replace('_', DIRECTORY_SEPARATOR, $class);

        $file = __DIR__ . '/some/path/' . $class . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }

    spl_autoload_register('my_autoloader');
?>
</body>
</html>