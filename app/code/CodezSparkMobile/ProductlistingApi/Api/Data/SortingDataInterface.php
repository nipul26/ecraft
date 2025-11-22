<?php
namespace CodezSparkMobile\ProductlistingApi\Api\Data;

interface SortingDataInterface
{

    public const CODE = 'code';
    public const LABEL = 'label';

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label.
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);
}
