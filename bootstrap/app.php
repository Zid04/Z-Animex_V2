<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->encryptCookies(except: [
            'appearance',
            'sidebar_state',
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 401 - Non authentifié
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            return Inertia::render('errors/401')
                ->toResponse($request)
                ->setStatusCode(401);
        });

        // 403 - Accès refusé
$exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
    if ($e->getStatusCode() === 403) {
        return Inertia::render('errors/403', [
            'message' => $e->getMessage() ?: "Vous n'avez pas accès à cette ressource."
        ])->toResponse($request)->setStatusCode(403);
    }
    return null;
});
        // 404 - Page non trouvée
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return Inertia::render('errors/404')
                ->toResponse($request)
                ->setStatusCode(404);
        });

        // 419 - Session expirée
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return Inertia::render('errors/419')
                ->toResponse($request)
                ->setStatusCode(419);
        });

        // 429 - Trop de requêtes
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, $request) {
            return Inertia::render('errors/429')
                ->toResponse($request)
                ->setStatusCode(429);
        });

       // 500 - Erreur serveur
$exceptions->render(function (\Throwable $e, $request) {
    // Ne pas intercepter les exceptions déjà gérées
    if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException ||
    $e instanceof \Illuminate\Auth\AuthenticationException ||
        $e instanceof \Illuminate\Auth\AuthenticationException ||
        $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
        $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
        $e instanceof \Illuminate\Session\TokenMismatchException ||
        $e instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException) {
        return null; //  laisser le bon handler prendre le relais
    }

    return Inertia::render('errors/500')
        ->toResponse($request)
        ->setStatusCode(500);
});
    })
    ->create();
