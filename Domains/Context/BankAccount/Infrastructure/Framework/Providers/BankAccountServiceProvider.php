<?php

namespace Domains\Context\BankAccount\Infrastructure\Framework\Providers;

use Domains\Context\BankAccount\Application\EventHandlers\Account\AccountCreatedEventHandler;
use Domains\Context\BankAccount\Application\EventHandlers\Account\AccountRejectedEventHandler;
use Domains\Context\BankAccount\Application\UseCases\Account\CreateAccountUseCase;
use Domains\Context\BankAccount\Application\UseCases\Account\ICreateAccountUseCase;
use Domains\Context\BankAccount\Application\UseCases\Balance\IRecalculateBalanceUseCase;
use Domains\Context\BankAccount\Application\UseCases\Balance\RecalculateBalanceUseCase;
use Domains\Context\BankAccount\Domain\Model\Account\AccountEntity;
use Domains\Context\BankAccount\Infrastructure\Framework\Entities\AccountModel;
use Domains\Context\BankAccount\Infrastructure\Framework\DataAccess\Repositories\AccountRepository;
use Domains\Context\BankAccount\Domain\Model\Account\IAccountRepository;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class BankAccountServiceProvider extends ServiceProvider
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

        ## USE CASE - Create a new account  ##
        $this->app->singleton(
            ICreateAccountUseCase::class,
            function () {
                $domainEventBus = new DomainEventBus();
                $domainEventBus->subscribe(new AccountCreatedEventHandler());
                $domainEventBus->subscribe(new AccountRejectedEventHandler());
                $account = new AccountEntity($domainEventBus);
                $accountModel = new AccountModel();
                $accountRepository = new AccountRepository($accountModel);
                return new CreateAccountUseCase($account, $accountRepository);
            }
        );

        ## USE CASE - Recalculate balance for any account  ##
        $this->app->singleton(
            IRecalculateBalanceUseCase::class,
            function () {
                $domainEventBus = new DomainEventBus();
                $domainEventBus->subscribe(new AccountCreatedEventHandler());
                $domainEventBus->subscribe(new AccountRejectedEventHandler());
                $account = new AccountEntity($domainEventBus);
                $accountModel = new AccountModel();
                $accountRepository = new AccountRepository($accountModel);
                return new RecalculateBalanceUseCase($account, $accountRepository);
            }
        );

        $this->app->singleton(
            IAccountRepository::class,
            function () {
                $accountModel = new AccountModel();
                return new AccountRepository($accountModel);
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
                __DIR__ . '/../Config/config.php' => config_path('BankAccount.php'),
            ],
            'config'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'BankAccount'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/BankAccount');

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
                        return $path . '/modules/BankAccount';
                    },
                    \Config::get('view.paths')
                ),
                [$sourcePath]
            ),
            'BankAccount'
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/BankAccount');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'BankAccount');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'BankAccount');
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
