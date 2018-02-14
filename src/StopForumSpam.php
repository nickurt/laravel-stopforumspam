<?php

namespace nickurt\StopForumSpam;

use \GuzzleHttp\Client;
use \nickurt\StopForumSpam\Exception\MalformedURLException;

class StopForumSpam
{
    /**
     * @var string
     */
    protected $apiUrl = 'https://api.stopforumspam.org/api';

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param $apiUrl
     * @return $this
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
        return $this;
    }
}
