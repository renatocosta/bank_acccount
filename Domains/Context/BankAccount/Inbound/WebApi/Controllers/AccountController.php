<?php


namespace Domains\Context\BankAccount\Inbound\WebApi\Controllers;

use App\Http\Controllers\Controller;
use Domains\Context\BankAccount\Application\UseCases\Account\CreateAccountInput;
use Domains\Context\BankAccount\Application\UseCases\Account\ICreateAccountUseCase;
use Domains\Context\BankAccount\Infrastructure\Framework\Transformers\AccountErrorsResource;
use Domains\Context\BankAccount\Infrastructure\Framework\Transformers\AccountResource;
use Domains\Context\BankAccount\Domain\Model\Account\IAccountRepository;
use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{

    /**
     * @OA\Post(
     ** path="/bankaccount/account",
     *   tags={"Account"},
     *   summary="Create a new account",
     *   operationId="account",
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="customer_id",
     *         @OA\Schema(
     *             type="integer",
     *             enum={1, 2}     
     *         ),
     *     ),
     *  
     *   @OA\Parameter(
     *      name="account_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
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
    public function create(Request $request, ICreateAccountUseCase $createAccountUseCase)
    {
        $input = new CreateAccountInput($request->customer_id, $request->account_name, new MessageHandler());
        $createAccountUseCase->execute($input);

        if ($input->modelState->isValid()) {
            return new AccountResource($createAccountUseCase->account);
        }

        return (new AccountErrorsResource($input->modelState->errors))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @OA\Get(
     ** path="/bankaccount/account/{account_id}/transactions",
     *   tags={"Account"},
     *   summary="List all transactions by account",
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
    public function allTransactions(Request $request, IAccountRepository $accountRepository)
    {
        return $accountRepository->findTransactions($request->get('account_id'), false);
    }
}
