<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CodezSparkMobile\ProductlistingApi\Api\Data;

interface ProductBadgesInterface
{

    public const TITLE = 'title';

    public const COLOR = 'color';

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get Color
     *
     * @return string
     */
    public function getColor();

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title);

    /**
     * Set Color
     *
     * @api
     * @param string $color
     * @return $this
     */
    public function setColor(string $color);
}
