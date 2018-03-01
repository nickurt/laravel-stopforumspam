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
     * @var
     */
    protected $ip;

    /**
     * @var
     */
    protected $email;

    /**
     * @var
     */
    protected $username;

    /**
     * @var
     */
    protected $frequency = 10;

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
        if (filter_var($apiUrl, FILTER_VALIDATE_URL) === false) {
            throw new MalformedURLException();
        }

        $this->apiUrl = $apiUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param $frequency
     * @return $this
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSpamEmail()
    {
        $result = cache()->remember('laravel-stopforumspam-'.str_slug($this->getEmail()), 10, function () {
            $response = $this->getResponseData(
                sprintf('%s?email=%s&json',
                    $this->getApiUrl(),
                    $this->getEmail()
                ));

            return json_decode((string) $response->getBody());
        });

        if(isset($result->success) && $result->success) {
            if(isset($result->email->appears) && $result->email->appears) {
                if($result->email->frequency >= $this->getFrequency()) {
                    event(new \nickurt\StopForumSpam\Events\IsSpamEmail($this->getEmail()));

                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSpamIp()
    {
        $result = cache()->remember('laravel-stopforumspam-'.str_slug($this->getIp()), 10, function () {
            $response = $this->getResponseData(
                sprintf('%s?ip=%s&json',
                    $this->getApiUrl(),
                    $this->getIp()
                ));

            return json_decode((string) $response->getBody());
        });

        if(isset($result->success) && $result->success) {
            if(isset($result->ip->appears) && $result->ip->appears) {
                if($result->ip->frequency >= $this->getFrequency()) {
                    event(new \nickurt\StopForumSpam\Events\IsSpamIp($this->getIp()));

                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSpamUsername()
    {
        $result = cache()->remember('laravel-stopforumspam-'.str_slug($this->getUsername()), 10, function () {
            $response = $this->getResponseData(
                sprintf('%s?username=%s&json',
                    $this->getApiUrl(),
                    $this->getUsername()
                ));

            return json_decode((string) $response->getBody());
        });

        if(isset($result->success) && $result->success) {
            if(isset($result->username->appears) && $result->username->appears) {
                if($result->username->frequency >= $this->getFrequency()) {
                    event(new \nickurt\StopForumSpam\Events\IsSpamUsername($this->getUsername()));

                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * @param $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function getResponseData($url)
    {
        return (new Client())->get($url);
    }
}
