<?php
namespace CodezSparkMobile\ProductlistingApi\Api\Data;

interface OptionInterface
{

    public const ID = 'id';
    public const LABEL = 'label';

    /**
     * Get id.
     *
     * @return string
     */
    public function getId();

    /**
     * Set id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);

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
