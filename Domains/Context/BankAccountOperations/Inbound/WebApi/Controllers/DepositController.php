<?php


namespace Domains\Context\BankAccountOperations\Inbound\WebApi\Controllers;

use App\Http\Controllers\Controller;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve\ApproveDepositInput;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve\IApproveDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\IPlaceDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\PlaceDepositInput;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers\TransactionErrorsResource;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers\TransactionResource;
use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepositController extends Controller
{

    /**
     * @OA\Post(
     ** path="/bankaccountoperations/deposit",
     *   tags={"Deposit"},
     *   summary="Place a new deposit",
     *   operationId="Place a new deposit",
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
     *   @OA\Parameter(
     *      name="description",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * 
     *   @OA\Parameter(
     *      name="check_path_file",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
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
    public function create(Request $request, IPlaceDepositUseCase $placeDepositUseCase)
    {
        $input = new PlaceDepositInput($request->account_id, $request->balance, $request->description, $request->check_path_file, new MessageHandler());
        $placeDepositUseCase->execute($input);

        if ($input->modelState->isValid()) {
            return new TransactionResource($placeDepositUseCase->transaction);
        }

        return (new TransactionErrorsResource($input->modelState->errors))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @OA\Patch(
     *   path="/bankaccountoperations/deposit/{transaction_id}/approve",
     *   tags={"Deposit"},
     *   summary="Approve a deposit",
     *     @OA\Parameter(
     *         in="path",
     *         name="transaction_id",
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
     *
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, IApproveDepositUseCase $approveDepositUseCase)
    {

        $input = new ApproveDepositInput($request->id, new MessageHandler());
        $approveDepositUseCase->execute($input);

        if ($input->modelState->isValid()) {
            return new TransactionResource($approveDepositUseCase->transaction);
        }

        return (new TransactionErrorsResource($input->modelState->errors))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
