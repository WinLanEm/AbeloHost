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
use App\Presentation\Http\ExceptionHandler;
use App\Presentation\Http\HttpApplication;
use App\Presentation\Http\Router;
use App\Presentation\View\BlogViewData;

$projectDirectory = dirname(__DIR__);
$autoloadPath = $projectDirectory . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException('Composer dependencies are not installed. Run composer install.');
}

require_once $autoloadPath;

/** @var \Closure(?\PDO=): HttpApplication $createApplication */
$createApplication = static function (?\PDO $pdo = null) use ($projectDirectory): HttpApplication {
    if ($pdo === null) {
        /** @var array{host: string, port: int, database: string, username: string, password: string} $databaseConfig */
        $databaseConfig = require $projectDirectory . '/config/database.php';

        $connection = new PdoConnection(
            host: $databaseConfig['host'],
            port: $databaseConfig['port'],
            database: $databaseConfig['database'],
            username: $databaseConfig['username'],
            password: $databaseConfig['password'],
        );
        $pdo = $connection->getConnection();
    }

    /** @var array{debug: bool} $applicationConfig */
    $applicationConfig = require $projectDirectory . '/config/application.php';
    /** @var array<class-string<Throwable>, array{statusCode: int, publicMessage: string}> $httpExceptionConfig */
    $httpExceptionConfig = require $projectDirectory . '/config/http_exceptions.php';
    /** @var array{templateDirectory: string, compileDirectory: string} $viewConfig */
    $viewConfig = require $projectDirectory . '/config/view.php';

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

    return new HttpApplication(
        router: $router,
        exceptionHandler: new ExceptionHandler(
            renderer: $renderer,
            debug: $applicationConfig['debug'],
            exceptionMappings: $httpExceptionConfig,
        ),
    );
};

return $createApplication;
