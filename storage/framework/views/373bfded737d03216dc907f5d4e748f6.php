<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo e(config('app.name', 'خطّط')); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Base URLs -->
    <meta name="base-url" content="<?php echo e(request()->getBaseUrl()); ?>">
    <meta name="api-base-url" content="<?php echo e(url('api/v1')); ?>">
    
    <!-- Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="antialiased">
    <div id="app"></div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\learn\khattit-platform\resources\views/app.blade.php ENDPATH**/ ?>