<?php

namespace nickurt\StopForumSpam;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use nickurt\StopForumSpam\Events\IsSpamEmail;
use nickurt\StopForumSpam\Events\IsSpamIp;
use nickurt\StopForumSpam\Events\IsSpamUsername;
use nickurt\StopForumSpam\Exception\MalformedURLException;

class StopForumSpam
{
    /** @var string */
    protected $apiUrl = 'https://api.stopforumspam.org/api';

    /** @var string */
    protected $email;

    /** @var int */
    protected $frequency = 10;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $username;

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function isSpamEmail()
    {
        $result = cache()->remember('laravel-stopforumspam-'.Str::slug($this->getEmail()), 10, function () {
            return $this->getResponseData(
                sprintf('%s?email=%s&json',
                    $this->getApiUrl(),
                    $this->getEmail()
                ));
        });

        if (isset($result['success']) && $result['success']) {
            if (isset($result['email']['appears']) && $result['email']['appears']) {
                if ($result['email']['frequency'] >= $this->getFrequency()) {
                    event(new IsSpamEmail($this->getEmail(), $result['email']['frequency']));

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param  string  $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return array|mixed
     */
    protected function getResponseData($url)
    {
        return Http::get($url)->json();
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param  string  $apiUrl
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
     * @return int
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param  int  $frequency
     * @return $this
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function isSpamIp()
    {
        $result = cache()->remember('laravel-stopforumspam-'.Str::slug($this->getIp()), 10, function () {
            return $this->getResponseData(
                sprintf('%s?ip=%s&json',
                    $this->getApiUrl(),
                    $this->getIp()
                ));
        });

        if (isset($result['success']) && $result['success']) {
            if (isset($result['ip']['appears']) && $result['ip']['appears']) {
                if ($result['ip']['frequency'] >= $this->getFrequency()) {
                    event(new IsSpamIp($this->getIp(), $result['ip']['frequency']));

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param  string  $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function isSpamUsername()
    {
        $result = cache()->remember('laravel-stopforumspam-'.Str::slug($this->getUsername()), 10, function () {
            return $this->getResponseData(
                sprintf('%s?username=%s&json',
                    $this->getApiUrl(),
                    $this->getUsername()
                ));
        });

        if (isset($result['success']) && $result['success']) {
            if (isset($result['username']['appears']) && $result['username']['appears']) {
                if ($result['username']['frequency'] >= $this->getFrequency()) {
                    event(new IsSpamUsername($this->getUsername(), $result['username']['frequency']));

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param  string  $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}
