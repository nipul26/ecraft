<?php
/**
 * CodezSpark
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the codezspark.com license that is
 * available through the world-wide-web at this URL:
 * https://codezspark.com/end-user-license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    CodezSpark
 * @package     Codezspark_Faq
 * @copyright   Copyright (c) CodezSpark (https://codezspark.com/)
 * @license     https://codezspark.com/end-user-license-agreement
 */

namespace Codezspark\Faq\Model\ResourceModel\Faq;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codezspark\Faq\Model\Faq as FaqModel;
use Codezspark\Faq\Model\ResourceModel\Faq as FaqResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    public $_idFieldName = 'faq_id';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            FaqModel::class,
            FaqResourceModel::class
        );
    }
}
