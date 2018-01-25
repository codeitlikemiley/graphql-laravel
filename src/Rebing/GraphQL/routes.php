<?php

Route::group([
    'prefix'        => config('graphql.prefix'),
    'middleware'    => config('graphql.middleware', [])
], function($router)
{
    // Routes
    $routes = config('graphql.routes');
    $queryRoute = null;
    $mutationRoute = null;
    if(is_array($routes))
    {
        $queryRoute = array_get($routes, 'query');
        $mutationRoute = array_get($routes, 'mutation');
    }
    else
    {
        $queryRoute = $routes;
        $mutationRoute = $routes;
    }
    
    // Controllers
    $controllers = config('graphql.controllers', \Rebing\GraphQL\GraphQLController::class . '@query');
    $queryController = null;
    $mutationController = null;
    if(is_array($controllers))
    {
        $queryController = array_get($controllers, 'query');
        $mutationController = array_get($controllers, 'mutation');
    }
    else
    {
        $queryController = $controllers;
        $mutationController = $controllers;
    }

    $schemaParameterPattern = '/\{\s*graphql\_schema\s*\?\s*\}/';

    // Query
    if ($queryRoute) {
        // Remove optional parameter in Lumen. Instead, creates two routes.
        if (!$router instanceof \Illuminate\Routing\Router &&
            preg_match($schemaParameterPattern, $queryRoute)
        ) {
            $router->get(preg_replace($schemaParameterPattern, '', $queryRoute), array(
                'as' => 'graphql.query',
                'uses' => $queryController
            ));
            $router->get(preg_replace($schemaParameterPattern, '{graphql_schema}', $queryRoute), array(
                'as' => 'graphql.query.with_schema',
                'uses' => $queryController
            ));
            $router->post(preg_replace($schemaParameterPattern, '', $queryRoute), array(
                'as' => 'graphql.query.post',
                'uses' => $queryController
            ));
            $router->post(preg_replace($schemaParameterPattern, '{graphql_schema}', $queryRoute), array(
                'as' => 'graphql.query.post.with_schema',
                'uses' => $queryController
            ));
        } else {
            $router->get($queryRoute, array(
                'as' => 'graphql.query',
                'uses' => $queryController
            ));
            $router->post($queryRoute, array(
                'as' => 'graphql.query.post',
                'uses' => $queryController
            ));
        }
    }

    // Mutation
    if ($mutationRoute && $mutationRoute !== $queryRoute) {
        // Remove optional parameter in Lumen. Instead, creates two routes.
        if (!$router instanceof \Illuminate\Routing\Router &&
            preg_match($schemaParameterPattern, $mutationRoute)
        ) {
            $router->post(preg_replace($schemaParameterPattern, '', $mutationRoute), array(
                'as' => 'graphql.mutation',
                'uses' => $mutationController
            ));
            $router->post(preg_replace($schemaParameterPattern, '{graphql_schema}', $mutationRoute), array(
                'as' => 'graphql.mutation.with_schema',
                'uses' => $mutationController
            ));
            $router->get(preg_replace($schemaParameterPattern, '', $mutationRoute), array(
                'as' => 'graphql.mutation.get',
                'uses' => $mutationController
            ));
            $router->get(preg_replace($schemaParameterPattern, '{graphql_schema}', $mutationRoute), array(
                'as' => 'graphql.mutation.get.with_schema',
                'uses' => $mutationController
            ));
        } else {
            $router->post($mutationRoute, array(
                'as' => 'graphql.mutation',
                'uses' => $mutationController
            ));
            $router->get($mutationRoute, array(
                'as' => 'graphql.mutation.get',
                'uses' => $mutationController
            ));
        }
    }
});
