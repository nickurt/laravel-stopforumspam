<?php

namespace nickurt\StopForumSpam;

/**
 * @method static \nickurt\StopForumSpam\StopForumSpam setClient(\GuzzleHttp\ClientInterface $client)
 * @method static \GuzzleHttp\ClientInterface getClient()
 *
 * @see \nickurt\StopForumSpam\StopForumSpam
 */
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
