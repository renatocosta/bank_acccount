<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\Providers;

use Domains\Context\BankAccountOperations\Application\EventHandlers\Account\AccountCreatedEventHandler;
use Domains\Context\BankAccountOperations\Application\EventHandlers\Account\AccountRejectedEventHandler;
use Domains\Context\BankAccountOperations\Application\EventHandlers\Transaction\TransactionCreatedEventHandler;
use Domains\Context\BankAccountOperations\Application\EventHandlers\Transaction\TransactionRejectedEventHandler;
use Domains\Context\BankAccountOperations\Application\UseCases\Account\CreateAccountUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Account\ICreateAccountUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Balance\IRecalculateBalanceUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Balance\RecalculateBalanceUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Transaction\IPlaceDepositUseCase;
use Domains\Context\BankAccountOperations\Application\UseCases\Transaction\PlaceDepositUseCase;
use Domains\Context\BankAccountOperations\Domain\Model\Account\AccountEntity;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\TransactionEntity;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Entities\AccountModel;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\DataAccess\Repositories\AccountRepository;
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
                $domainEventBus->subscribe(new TransactionCreatedEventHandler());
                $domainEventBus->subscribe(new TransactionRejectedEventHandler());
                $transaction = new TransactionEntity($domainEventBus);
                $transactionModel = new TransactionsModel();
                $transactionRepository = new TransactionRepository($transactionModel);
                return new PlaceDepositUseCase($transaction, $transactionRepository);
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
