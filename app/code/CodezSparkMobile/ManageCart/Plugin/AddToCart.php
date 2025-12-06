<?php
namespace CodezSparkMobile\ManageCart\Plugin;

use Magento\Quote\Api\CartItemRepositoryInterface;
use CodezSparkMobile\Logger\Logger\Logger as MobileLogger;
use Magento\Framework\Webapi\Rest\Response as HttpResponse;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

class AddToCart
{
    protected $mobileLogger;
    protected $response;
    protected $productRepository;
    protected $configurableType;

    public function __construct(
        MobileLogger $mobileLogger,
        HttpResponse $response,
        ProductRepositoryInterface $productRepository,
        Configurable $configurableType
    ) {
        $this->mobileLogger = $mobileLogger;
        $this->response = $response;
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
    }

    /**
     * After plugin for Add to Cart API
     */
    public function afterSave(
        CartItemRepositoryInterface $subject,
        $result,
        $cartItem
    ) {
        try {
            /** Child (actual selected variant) */
            $childProductId = $result->getProductId();
            $childProduct   = $this->productRepository->getById($childProductId);

            /** Check configurable parent */
            $parentIds = $this->configurableType->getParentIdsByChild($childProductId);
            $isConfigurable = !empty($parentIds);

            $selectedAttributes = [];

            if ($isConfigurable) {

                $parentProduct = $this->productRepository->getById($parentIds[0]);
                $productName   = $parentProduct->getName();
                $parentSku     = $parentProduct->getSku();

                /** Get selected configurable options */
                $extAttributes = $result->getProductOption()->getExtensionAttributes();

                if ($extAttributes && $extAttributes->getConfigurableItemOptions()) {
                    $options = $extAttributes->getConfigurableItemOptions();

                    foreach ($options as $option) {

                        // When returned as array
                        if (is_array($option)) {
                            $selectedAttributes[] = [
                                'attribute_id' => $option['option_id'] ?? null,
                                'option_value' => $option['option_value'] ?? null
                            ];
                        }

                        // When returned as object
                        elseif (is_object($option)) {
                            $selectedAttributes[] = [
                                'attribute_id' => $option->getOptionId(),
                                'option_value' => $option->getOptionValue()
                            ];
                        }
                    }
                }

            } else {
                /** Simple product */
                $productName = $result->getName();
                $parentSku   = $result->getSku();
            }

            /** Prepare response */
            $responseArray = [
                'status'  => true,
                'message' => sprintf('You added %s to your shopping cart.', $productName),
                'response' => [
                    'quote_id'   => $result->getQuoteId(),
                    'item_id'    => $result->getItemId(),
                    'name'       => $productName,
                    'sku'        => $parentSku,
                    'child_sku'  => $childProduct->getSku(),
                    'qty'        => $result->getQty(),
                    'type'       => $isConfigurable ? 'configurable' : 'simple',
                    'attributes' => $selectedAttributes
                ]
            ];

            $this->mobileLogger->info('Add to cart success', $responseArray);

        } catch (\LocalizedException $e) {

            $responseArray = [
                'status' => false,
                'message' => $e->getMessage(),
                'response' => []
            ];

            $this->mobileLogger->error($e->getMessage());

        } catch (\Exception $e) {

            $responseArray = [
                'status' => false,
                'message' => 'Something went wrong while adding the item to the cart.',
                'response' => []
            ];

            $this->mobileLogger->error($e->getMessage());
        }

        return $this->response->setBody(json_encode($responseArray))->sendResponse();
    }
}
