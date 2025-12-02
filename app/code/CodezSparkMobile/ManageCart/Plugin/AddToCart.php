<?php
namespace CodezSparkMobile\ManageCart\Plugin;

use Magento\Quote\Api\CartItemRepositoryInterface;
use CodezSparkMobile\Logger\Logger\Logger as MobileLogger;
use Magento\Framework\Webapi\Rest\Response as HttpResponse;

class AddToCart
{
    protected $mobileLogger;

    protected $response;

    public function __construct(
        MobileLogger $mobileLogger,
        HttpResponse $response
    ) {
        $this->mobileLogger = $mobileLogger;
        $this->response = $response;
    }

    /**
     * After plugin for Add to Cart API
     *
     * @param CartItemRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartItemInterface $result
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return array
     */
    public function afterSave(
        CartItemRepositoryInterface $subject,
        $result,
        $cartItem
    ) {
        try {
            $productName = $result->getName() ?: 'Product';
            $quoteId = $result->getQuoteId();

            $responseArray = [
                'status' => true,
                'message' => sprintf('You added %s to your shopping cart.', $productName),
                'response' => [
                    'quote_id' => $quoteId,
                    'item_id' => $result->getItemId(),
                    'name' => $result->getName(),
                    'sku' => $result->getSku(),
                    'qty' => $result->getQty()
                ]
            ];

            $this->mobileLogger->info('Add to cart success', $responseArray);

            // return $responseArray;
        }catch (\LocalizedException $e) {
            $responseArray = [
                'status' => false,
                'message' => $e->getMessage(),
                'response' => []
            ];
            $this->mobileLogger->error('Add to Cart Localized Error: ' . $e->getMessage());

        }catch (\Exception $e) {
            $this->mobileLogger->error('Add to cart failed: ' . $e->getMessage());
            $responseArray = [
                'status' => false,
                'message' => 'Something went wrong while adding the item to the cart.',
                'response' => []
            ];
        }

        return $this->response->setBody(json_encode($responseArray))->sendResponse();

    }
}
