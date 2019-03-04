<?php

namespace R64\LaravelThinq;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class ThinqChannel
{
    protected $thinq;
    protected $events;

    public function __construct(Thinq $thinq, Dispatcher $events)
    {
        $this->thinq = $thinq;
        $this->events = $events;
    }

    public function send($notifiable, Notification $notification)
    {
        // Since thinq is restricted to IP, disable or enable api call when testing
        if ($this->shouldDisableApiCall()) {
            return;
        }

        $message = $notification->toThinq($notifiable);

        if(property_exists($notification, 'silent') && $notification->silent) {
            $this->thinq->withMessage($message)->sentSilentSms();
        } else {
            $this->thinq->withMessage($message)->sentSms();
        }
    }

    private function shoudDisableApiCall()
    {
        return app()->environment('local') && config('thinq.disable_api_calls');
    }
}