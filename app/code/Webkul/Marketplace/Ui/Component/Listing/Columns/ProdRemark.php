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
namespace Webkul\Marketplace\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProdRemark extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * Constructor.
     *
     * @param ContextInterface                  $context
     * @param UiComponentFactory                $uiComponentFactory
     * @param UrlInterface                      $urlBuilder
     * @param \Webkul\Marketplace\Helper\Data   $mpHelper
     * @param array                             $components
     * @param array                             $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->mpHelper = $mpHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $helper = $this->mpHelper;
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $pending = \Webkul\Marketplace\Model\Product::STATUS_PENDING;
                $item[$fieldName.'_html'] = "<button class='button'><span>".__('Remark')."</span></button>";
                $item[$fieldName.'_title'] = __('Remark this product?');
                $item[$fieldName.'_submitlabel'] = __('Remark');
                $item[$fieldName.'_cancellabel'] = __('Reset');
                $item[$fieldName.'_productid'] = $item['mageproduct_id'];
                $item[$fieldName.'_sellerid'] = $item['seller_id'];
                $item[$fieldName.'_formaction'] = $this->urlBuilder->getUrl('marketplace/product/remark');
                if ($item["status"] != $pending
                || (!$helper->getIsProductApproval() && !$helper->getIsProductEditApproval())) {
                    $item[$fieldName.'_html'] = "--";
                }
            }
        }

        return $dataSource;
    }
}
