<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Block\Account\Dashboard;

use Magento\Framework\View\Element\Template\Context;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Helper\Dashboard\Data as HelperDashboard;
use Webkul\Marketplace\Model\ResourceModel\Feedback\CollectionFactory;

class ReviewChart extends \Magento\Framework\View\Element\Template
{
    /**
     * @param Context           $context
     * @param HelperData        $helper
     * @param HelperDashboard   $helperDashboard
     * @param CollectionFactory $collectionFactory
     * @param array             $data
     */
    public function __construct(
        protected Context $context,
        protected HelperData $helper,
        protected HelperDashboard $helperDashboard,
        protected CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get seller statistics data
     *
     * @param string $type
     * @return array
     */
    public function getSellerStatisticsGraphUrl($type = "feed_value")
    {
        $feedbackCollection = $this->collectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $this->helper->getCustomerId()
        );
        $allReviewCountArr = $feedbackCollection->getAllReviewCount($type);
        $allFiveStarReviewCount = $feedbackCollection->getAllReviewCount($type, 100);
        $allFourStarReviewCount = $feedbackCollection->getAllReviewCount($type, 80);
        $allThreeStarReviewCount = $feedbackCollection->getAllReviewCount($type, 60);
        $allTwoStarReviewCount = $feedbackCollection->getAllReviewCount($type, 40);
        $allOneStarReviewCount = $feedbackCollection->getAllReviewCount($type, 20);
        $allFiveStarReview = $allFourStarReview = $allThreeStarReview = $allTwoStarReview = $allOneStarReview = 0;

        if (!empty($allReviewCountArr[0])) {
            $allReviewCount = $allReviewCountArr[0];
            if (!empty($allFiveStarReviewCount[0])) {
                $allFiveStarReview = (100 * $allFiveStarReviewCount[0]) / $allReviewCount;
            }
            if (!empty($allFourStarReviewCount[0])) {
                $allFourStarReview = (100 * $allFourStarReviewCount[0]) / $allReviewCount;
            }
            if (!empty($allThreeStarReviewCount[0])) {
                $allThreeStarReview = (100 * $allThreeStarReviewCount[0]) / $allReviewCount;
            }
            if (!empty($allTwoStarReviewCount[0])) {
                $allTwoStarReview = (100 * $allTwoStarReviewCount[0]) / $allReviewCount;
            }
            if (!empty($allOneStarReviewCount[0])) {
                $allOneStarReview = (100 * $allOneStarReviewCount[0]) / $allReviewCount;
            }
        }
        $getReviewPercentageArr = [
            5 => round($allFiveStarReview),
            4 => round($allFourStarReview),
            3 => round($allThreeStarReview),
            2 => round($allTwoStarReview),
            1 => round($allOneStarReview)
        ];
        return $getReviewPercentageArr;
    }
}
