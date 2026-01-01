<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {

        Response::macro('success', function (string $message, array $resource = []) {
            return response()->json(
                [
                    'status' => 'success',
                    'ok' => true,
                    'text' => $message,
                    'resource' => $resource,
                ],
                200
            );
        });

        Response::macro('warning', function (string $message, array $resource = []) {
            return response()->json(
                [
                    'status' => 'warning',
                    'ok' => true,
                    'text' => $message,
                    'resource' => $resource,
                ],
                200
            );
        });

        Response::macro('created', function (string $message, array $resource = []) {
            return response()->json(
                [
                    'status' => 'success',
                    'created' => true,
                    'text' => $message,
                    'resource' => $resource,
                ],
                201
            );
        });

        Response::macro('error', function (string $message, array $resource = []) {
            return response()->json(
                [
                    'status' => 'error',
                    'precondition failed' => true,
                    'text' => $message,
                    'resource' => $resource,
                ],
                400
            );
        });

        Response::macro('notfound', function (string $message, array $resource = []) {
            return response()->json(
                [
                    'status' => 'error',
                    'not_found' => true,
                    'text' => $message,
                    'resource' => $resource,
                ],
                404
            );
        });

        Response::macro('deleted', function (string $message, array $resource = []) {
            return response()->json(
                [
                    'status' => 'success',
                    'ok' => true,
                    'text' => $message,
                    'resource' => $resource,
                ],
                200 // 204 is valid delete response, but no message returned
            );
        });
    }
}
