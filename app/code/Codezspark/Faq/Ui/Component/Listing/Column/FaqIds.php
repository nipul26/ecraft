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
use Codezspark\Faq\Model\ResourceModel\Faq\CollectionFactory;

class FaqIds implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $faqCollection;

    /**
     * FaqIds constructor.
     * @param CollectionFactory $faqCollection
     */
    public function __construct(
        CollectionFactory $faqCollection
    ) {
        $this->faqCollection = $faqCollection;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $faqArr = [];
        $faqs = $this->faqCollection->create();

        foreach ($faqs as $faq) {
            $faqArr[] = ['value' => $faq->getFaqId(), 'label' => __($faq->getTitle())];
        }

        return $faqArr;
    }
}
