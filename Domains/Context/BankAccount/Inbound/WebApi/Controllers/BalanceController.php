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
