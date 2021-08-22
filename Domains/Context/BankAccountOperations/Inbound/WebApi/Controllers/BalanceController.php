<?php


namespace Domains\Context\BankAccountOperations\Inbound\WebApi\Controllers;

use App\Http\Controllers\Controller;
use Domains\Context\BankAccountOperations\Application\UseCases\Account\CreateAccountInput;
use Domains\Context\BankAccountOperations\Application\UseCases\Account\ICreateAccountUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Balance\IRecalculateBalanceUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Balance\RecalculateBalanceInput;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers\AccountErrorsResource;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers\AccountResource;
use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BalanceController extends Controller
{

    /**
     * @OA\Patch(
     *   path="/bankaccount/balance/{account_id}",
     *   tags={"Account"},
     *   summary="Recalculate the given account",
     *     @OA\Parameter(
     *         in="path",
     *         name="account_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     * 
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IRecalculateBalanceUseCase $recalculateBalanceUseCase)
    {

        $input = new RecalculateBalanceInput($request->account_id, new MessageHandler());
        $recalculateBalanceUseCase->execute($input);

        if ($input->modelState->isValid()) {
            return new AccountResource($recalculateBalanceUseCase->account);
        }

        return (new AccountErrorsResource($input->modelState->errors))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
