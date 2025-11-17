<?php
namespace CodezSparkMobile\ProductlistingApi\Api\Data;

interface FilterDataInterface
{

    public const CODE = 'code';
    public const LABEL = 'label';
    public const TYPE = 'type';
    public const OPTIONS = 'options';
    public const MIN_RANGE = 'minRange';
    public const MAX_RANGE = 'maxRange';

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

    /**
     * Get type.
     *
     * @return string
     */
    public function getType();

    /**
     * Set type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get options.
     *
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\OptionInterface[]
     */
    public function getOptions();

    /**
     * Set options.
     *
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\OptionInterface[] $optionData
     * @return $this
     */
    public function setOptions($optionData);

    /**
     * Get minRange.
     *
     * @return string
     */
    public function getMinRange();

    /**
     * Set minRange.
     *
     * @param string $minRange
     * @return $this
     */
    public function setMinRange($minRange);

    /**
     * Get maxRange.
     *
     * @return string
     */
    public function getMaxRange();

    /**
     * Set maxRange.
     *
     * @param string $maxRange
     * @return $this
     */
    public function setMaxRange($maxRange);
}
