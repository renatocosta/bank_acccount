<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\Providers;

use Domains\Context\BankAccountOperations\Application\EventHandlers\Deposit\TransactionApprovedEventHandler;
use Domains\Context\BankAccountOperations\Application\EventHandlers\Deposit\TransactionPlacedEventHandler;
use Domains\Context\BankAccountOperations\Application\EventHandlers\Deposit\TransactionRejectedEventHandler;
use Domains\Context\BankAccountOperations\Application\EventHandlers\Withdrawal\WithdrawalPlacedEventHandler;
use Domains\Context\BankAccountOperations\Application\EventHandlers\Withdrawal\WithdrawalRejectedEventHandler;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve\ApproveDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve\IApproveDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\IPlaceDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Deposit\PlaceDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Withdrawal\IWithdrawalUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Withdrawal\WithdrawalUseCase;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\TransactionEntity;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\DataAccess\Repositories\TransactionRepository;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Entities\TransactionsModel;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class BankAccountOperationsServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        ## USE CASE - Place a new deposit  ##
        $this->app->singleton(
            IPlaceDepositUseCase::class,
            function () {
                $domainEventBus = new DomainEventBus();
                $domainEventBus->subscribe(new TransactionPlacedEventHandler());
                $domainEventBus->subscribe(new TransactionRejectedEventHandler());
                $transaction = new TransactionEntity($domainEventBus);
                $transactionModel = new TransactionsModel();
                $transactionRepository = new TransactionRepository($transactionModel);
                return new PlaceDepositUseCase($transaction, $transactionRepository);
            }
        );

        ## USE CASE - Approve a deposit  ##
        $this->app->singleton(
            IApproveDepositUseCase::class,
            function () {
                $domainEventBus = new DomainEventBus();
                $domainEventBus->subscribe(new TransactionApprovedEventHandler);
                $domainEventBus->subscribe(new TransactionRejectedEventHandler());
                $transaction = new TransactionEntity($domainEventBus);
                $transactionModel = new TransactionsModel();
                $transactionRepository = new TransactionRepository($transactionModel);
                return new ApproveDepositUseCase($transaction, $transactionRepository);
            }
        );

        ## USE CASE - Place a withdrawal  ##
        $this->app->singleton(
            IWithdrawalUseCase::class,
            function () {
                $domainEventBus = new DomainEventBus();
                $domainEventBus->subscribe(new WithdrawalPlacedEventHandler);
                $domainEventBus->subscribe(new WithdrawalRejectedEventHandler());
                $transaction = new TransactionEntity($domainEventBus);
                $transactionModel = new TransactionsModel();
                $transactionRepository = new TransactionRepository($transactionModel);
                return new WithdrawalUseCase($transaction, $transactionRepository);
            }
        );

    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
            [
                __DIR__ . '/../Config/config.php' => config_path('BankAccountOperations.php'),
            ],
            'config'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'BankAccountOperations'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/BankAccountOperations');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes(
            [
                $sourcePath => $viewPath
            ],
            'views'
        );

        $this->loadViewsFrom(
            array_merge(
                array_map(
                    function ($path) {
                        return $path . '/modules/BankAccountOperations';
                    },
                    \Config::get('view.paths')
                ),
                [$sourcePath]
            ),
            'BankAccountOperations'
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/BankAccountOperations');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'BankAccountOperations');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'BankAccountOperations');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
