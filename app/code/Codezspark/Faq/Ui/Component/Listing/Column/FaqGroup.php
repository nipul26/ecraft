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

namespace Codezspark\Faq\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Codezspark\Faq\Model\ResourceModel\FaqGroup\CollectionFactory;

class FaqGroup implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $groupCollection;

    /**
     * FaqGroup constructor.
     * @param CollectionFactory $groupCollection
     */
    public function __construct(
        CollectionFactory $groupCollection
    ) {
        $this->groupCollection = $groupCollection;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groupArr = [];
        $groups = $this->groupCollection->create();

        foreach ($groups as $group) {
            $groupArr[] = ['value' => $group->getFaqGroupId(), 'label' => __($group->getGroupname())];
        }

        return $groupArr;
    }
}
