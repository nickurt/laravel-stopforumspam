<?php

namespace nickurt\StopForumSpam\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsSpamEmail implements Rule
{
    /** @var int */
    protected $frequency;

    /**
     * @param  int  $frequency
     */
    public function __construct($frequency = 10)
    {
        $this->frequency = $frequency;
    }

    /**
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function message()
    {
        return trans('stopforumspam::stopforumspam.it_is_currently_not_possible_to_register_with_your_specified_information_please_try_later_again');
    }

    /**
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     *
     * @throws \Exception
     */
    public function passes($attribute, $value)
    {
        /** @var \nickurt\StopForumSpam\StopForumSpam $stopForumSpam */
        $stopForumSpam = app('StopForumSpam');

        $stopForumSpam->setEmail($value)->setFrequency($this->frequency);

        return $stopForumSpam->isSpamEmail() ? false : true;
    }
}
