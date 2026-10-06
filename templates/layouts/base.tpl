<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$pageTitle|escape}</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <header class="site-header">
        <div class="container site-header__inner">
            <a class="site-logo" href="/" aria-label="PHP Blog — на главную">
                <span class="site-logo__mark">&lt;/&gt;</span>
                <span>PHP Blog</span>
            </a>
            <span class="site-header__tagline">Тестовое задание</span>
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            {block name="content"}{/block}
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>Тестовое задание</p>
        </div>
    </footer>
</body>
</html>
