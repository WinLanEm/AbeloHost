<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Presentation\Http\HttpApplication;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;

abstract class FeatureTestCase extends DatabaseTestCase
{
    private HttpApplication $application;

    /** @var array<string, mixed> */
    private array $server;

    /** @var array<string, mixed> */
    private array $queryParameters;

    protected function setUp(): void
    {
        parent::setUp();

        $this->server = $_SERVER;
        $this->queryParameters = $_GET;

        /** @var \Closure(?\PDO=): HttpApplication $createApplication */
        $createApplication = require dirname(__DIR__, 2) . '/bootstrap/application.php';
        $this->application = $createApplication($this->connection);
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $_GET = $this->queryParameters;

        parent::tearDown();
    }

    protected function get(string $uri): Response
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $uri;

        $queryString = parse_url($uri, PHP_URL_QUERY);
        $queryParameters = [];

        if (is_string($queryString)) {
            parse_str($queryString, $queryParameters);
        }

        $_GET = $queryParameters;

        return $this->application->handle(Request::fromGlobals());
    }
}
