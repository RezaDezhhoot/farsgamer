<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Article;
use App\Models\Card;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Payment;
use App\Models\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Observers\AddressObserver;
use App\Observers\ArticleObserver;
use App\Observers\CardObserver;
use App\Observers\NotificationObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderTransactionObserver;
use App\Observers\PaymentObserver;
use App\Observers\RequestObserver;
use App\Observers\TicketObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Address::observe(AddressObserver::class);
        Article::observe(ArticleObserver::class);
        Card::observe(CardObserver::class);
        Notification::observe(NotificationObserver::class);
        Order::observe(OrderObserver::class);
        OrderTransaction::observe(OrderTransactionObserver::class);
        Payment::observe(PaymentObserver::class);
        Request::observe(RequestObserver::class);
        Ticket::observe(TicketObserver::class);
        User::observe(UserObserver::class);
    }
}
