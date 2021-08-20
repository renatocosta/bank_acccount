<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        switch ($exception)
        {
            case $exception instanceof ModelNotFoundException && count($exception->getIds()) === 1:
                /* Handle model not found when querying a single entity */
                $id = array_first($exception->getIds());
                /*
                 * IF $existed == true : the user has requested a url for a model that was deleted
                 * IF $existed == false : the user has requested a url for a model that never existed (possible url manipulation)
                 */
                $existed = $exception->getModel()::onlyTrashed()->where('id', $id)->count() > 0;
                $entity = last(explode('\\', $exception->getModel()));
                $entitiesToReport = ['Tender', 'Project', 'Asset'];
                $detailKey = null;
                if(in_array($entity, $entitiesToReport, true)) {
                    $detailKey = $existed ? 'ENTITY_DELETED' : 'ENTITY_NOT_FOUND';
                }
                return response()->json([
                  'detailKey' =>  $detailKey,
                  'entity' => $entity
                ], 404);
                // TODO: log everything

            default:
                return parent::render($request, $exception);
        }

    }
}
