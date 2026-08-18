<?php
spl_autoload_register(function ($className) {
    // Tương thích ngược cho các object Model từ session cũ (chưa có namespace)
    if (in_array($className, ['User', 'Product', 'Category', 'Brand', 'Customer', 'Order', 'OrderDetail'])) {
        $modelFile = __DIR__ . '/models/' . $className . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            if (!class_exists($className, false)) {
                class_alias('Models\\' . $className, $className);
            }
            return;
        }
    }

    $prefixes = [
        'Controllers\\' => __DIR__ . '/controllers/',
        'Composers\\'   => __DIR__ . '/composers/',
        'DAO\\'         => __DIR__ . '/dao/',
        'Models\\'      => __DIR__ . '/models/',
        'Middleware\\'  => __DIR__ . '/middleware/',
        'Config\\'      => __DIR__ . '/config/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (str_starts_with($className, $prefix)) {
            $relativeClass = substr($className, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});
