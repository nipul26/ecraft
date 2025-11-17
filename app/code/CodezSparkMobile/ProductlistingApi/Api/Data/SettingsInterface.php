<?php

namespace CodezSparkMobile\ProductlistingApi\Api\Data;

/**
 * Interface SettingsInterface
 *
 * @api
 */
interface SettingsInterface
{
    public const DATA_CODE = 'code';
    public const DATA_MESSAGE = 'message';

    /**
     * Get code
     *
     * @return int
     */
    public function getCode();

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set Code
     *
     * @param int $code
     * @return $this
     */
    public function setCode(int $code);

    /**
     * Set Message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message);
}
