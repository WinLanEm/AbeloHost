<?php

declare(strict_types=1);

use App\Application\Article\GetArticlePage;
use App\Application\Category\GetCategoryPage;
use App\Application\Home\GetHomePage;
use App\Infrastructure\Persistence\MySql\PdoArticleRepository;
use App\Infrastructure\Persistence\MySql\PdoCategoryRepository;
use App\Infrastructure\Persistence\MySql\PdoConnection;
use App\Infrastructure\View\SmartyRenderer;
use App\Presentation\Controller\ArticleController;
use App\Presentation\Controller\CategoryController;
use App\Presentation\Controller\HomeController;
use App\Presentation\Http\Router;
use App\Presentation\View\BlogViewData;

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException('Composer dependencies are not installed. Run composer install.');
}

require_once $autoloadPath;

/** @var array{host: string, port: int, database: string, username: string, password: string} $databaseConfig */
$databaseConfig = require __DIR__ . '/config/database.php';
/** @var array{templateDirectory: string, compileDirectory: string} $viewConfig */
$viewConfig = require __DIR__ . '/config/view.php';

$connection = new PdoConnection(
    host: $databaseConfig['host'],
    port: $databaseConfig['port'],
    database: $databaseConfig['database'],
    username: $databaseConfig['username'],
    password: $databaseConfig['password'],
);
$pdo = $connection->getConnection();
$articleRepository = new PdoArticleRepository($pdo);
$categoryRepository = new PdoCategoryRepository($pdo);

$renderer = new SmartyRenderer(
    templateDirectory: $viewConfig['templateDirectory'],
    compileDirectory: $viewConfig['compileDirectory'],
);
$viewData = new BlogViewData();

$router = new Router();
$router->get('/', new HomeController(
    getHomePage: new GetHomePage(
        categories: $categoryRepository,
        articles: $articleRepository,
    ),
    renderer: $renderer,
    viewData: $viewData,
));
$router->get('/categories/{id}', new CategoryController(
    getCategoryPage: new GetCategoryPage(
        categories: $categoryRepository,
        articles: $articleRepository,
    ),
    renderer: $renderer,
    viewData: $viewData,
));
$router->get('/articles/{id}', new ArticleController(
    getArticlePage: new GetArticlePage($articleRepository),
    renderer: $renderer,
    viewData: $viewData,
));

return $router;
