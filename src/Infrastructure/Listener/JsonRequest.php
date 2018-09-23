<?php

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequest
{
    public function __invoke(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if('json' !== $request->getContentType()) {
            return;
        }

        if(($content = $request->getContent()) === false) {
            return;
        }

        $data = json_decode($content, JSON_OBJECT_AS_ARRAY);

        if(json_last_error() !== JSON_ERROR_NONE) {
            @trigger_error('Error decoding json request: ' . json_last_error_msg());
            return;
        }

        $request->attributes->add($data);
    }
}