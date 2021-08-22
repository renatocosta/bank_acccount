<?php


namespace Domains\Context\BankAccountOperations\Inbound\WebApi\Controllers;

use App\Http\Controllers\Controller;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve\ApproveDepositInput;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve\IApproveDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Withdrawal\IWithdrawalUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Withdrawal\WithdrawalInput;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers\TransactionErrorsResource;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers\TransactionResource;
use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalController extends Controller
{

    /**
     * @OA\Post(
     ** path="/bankaccountoperations/withdrawal",
     *   tags={"Withdrawal"},
     *   summary="Place a new withdrawal",
     *   operationId="Place a new withdrawal",
     *
     *   @OA\Parameter(
     *      name="account_id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="integer"
     *      )
     *   ),
     *  
     *   @OA\Parameter(
     *      name="balance",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="float"
     *      )
     *   ),
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
    public function create(Request $request, IWithdrawalUseCase $withdrawalUseCase)
    {
        Log::info("INcoming");
        $input = new WithdrawalInput($request->account_id, $request->balance, new MessageHandler());
        $withdrawalUseCase->execute($input);

        if ($input->modelState->isValid()) {
            return new TransactionResource($withdrawalUseCase->transaction);
        }

        return (new TransactionErrorsResource($input->modelState->errors))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
