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
namespace Webkul\Marketplace\Ui\Component\Listing\Columns\Frontend;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Webkul\Marketplace\Model\ProductRemarkFactory;

class Remarks extends Column
{
    /**
     * Constructor.
     *
     * @param ContextInterface                  $context
     * @param UiComponentFactory                $uiComponentFactory
     * @param UrlInterface                      $urlBuilder
     * @param \Webkul\Marketplace\Helper\Data   $mpHelper
     * @param ProductRemarkFactory              $productRemark
     * @param array                             $components
     * @param array                             $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected UrlInterface $urlBuilder,
        protected \Webkul\Marketplace\Helper\Data $mpHelper,
        protected ProductRemarkFactory $productRemark,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->mpHelper = $mpHelper;
        $this->productRemark = $productRemark;
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
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $productRemark = $this->productRemark->create()->getCollection()
                                     ->addFieldToFilter("mageproduct_id", $item['mageproduct_id'])
                                     ->setOrder("entity_id", "DESC");
                    $item[$fieldName.'_data'] = "";
                    $item[$fieldName.'_html'] = "";
                    if ($productRemark->getSize()) {
                        $cloneRemark = clone $productRemark;
                        $size = $productRemark->getSize();
                        $productRemark->setPageSize(3);
                        $rowData = $cloneRemark->getData();
                        $html = '<ul class="wk-remark-list">';
                        foreach ($productRemark as $remark) {
                            $html.= '<li>'.$remark->getRemark().'</li>';
                        }
                        $html .= '</ul>';
                        if ($size > 3) {
                            $html .= '<a href="#">'.__("View All Remarks")."</a>";
                        }
                        $item[$fieldName.'_data'] = $this->formatData($rowData)??"";
                        $item[$fieldName.'_html'] = $html;
                    }
                    $item[$fieldName.'_title'] = __('Product Remarks');
                    $item[$fieldName.'_close'] = __('Close');
                }
            }
        }
        return $dataSource;
    }
    /**
     * Format date and time
     *
     * @param array $rowData
     *
     * @return array
     */
    public function formatData($rowData)
    {
        foreach ($rowData as $key => $row) {
            $rowData[$key]["created_at"] = date("Y-m-d h:i a", strtotime($row['created_at']));
        }
        return $rowData;
    }
}
