<?php


namespace Domains\Context\BankAccount\Inbound\WebApi\Controllers;

use App\Http\Controllers\Controller;
use Domains\Context\BankAccount\Application\UseCases\Balance\IRecalculateBalanceUseCase;
use Domains\Context\BankAccount\Application\UseCases\Balance\RecalculateBalanceInput;
use Domains\Context\BankAccount\Infrastructure\Framework\Transformers\AccountErrorsResource;
use Domains\Context\BankAccount\Infrastructure\Framework\Transformers\AccountResource;
use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BalanceController extends Controller
{
    /**
     * @OA\Patch(
     *   path="/bankaccount/balance/{account_id}/operation/{operation}/amount/{amount}",
     *   tags={"Account"},
     *   summary="Recalculate an account balance",
     *     @OA\Parameter(
     *         in="path",
     *         name="account_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     * 
     *     @OA\Parameter(
     *         in="path",
     *         name="amount",
     *         @OA\Schema(
     *             type="double",
     *         ),
     *     ),
     *  
     *     @OA\Parameter(
     *         in="path",
     *         name="operation",
     *         @OA\Schema(
     *             type="string",
     *             enum={"Deposit", "Withdrawal"}     
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
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IRecalculateBalanceUseCase $recalculateBalanceUseCase)
    {

        $input = new RecalculateBalanceInput($request->account_id, $request->operation, $request->amount, new MessageHandler());
        $recalculateBalanceUseCase->execute($input);

        if ($input->modelState->isValid()) {
            return new AccountResource($recalculateBalanceUseCase->account);
        }

        return (new AccountErrorsResource($input->modelState->errors))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
