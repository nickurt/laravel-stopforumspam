<?php

namespace nickurt\StopForumSpam;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'StopForumSpam';
    }
}
