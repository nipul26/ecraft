<?php

namespace Codezspark\Customer\Plugin;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Framework\Webapi\Rest\Response;
use Psr\Log\LoggerInterface;

class CountryRegionResponsePlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Response $response,
        LoggerInterface $logger
    ) {
        $this->response = $response;
        $this->logger   = $logger;
    }

    public function afterGetCountryInfo(
        CountryInformationAcquirerInterface $subject,
        CountryInformationInterface $result
    ) {
        try {
            if (!$result || !$result instanceof CountryInformationInterface) {
                $responseData = [
                    "status"  => false,
                    "message" => "Country details could not be fetched.",
                    "response" => null
                ];

                return $this->response->setBody(json_encode($responseData))->sendResponse();
            }

            $regions = [];
            if ($result->getAvailableRegions()) {
                foreach ($result->getAvailableRegions() as $region) {
                    $regions[] = [
                        'id'   => $region->getId(),
                        'code' => $region->getCode(),
                        'name' => $region->getName()
                    ];
                }
            }

            $responseData = [
                "status"  => true,
                "message" => "Country and its region details fetched successfully.",
                "response" => [
                    "id"                        => $result->getId(),
                    "two_letter_abbreviation"   => $result->getTwoLetterAbbreviation(),
                    "three_letter_abbreviation" => $result->getThreeLetterAbbreviation(),
                    "full_name_locale"          => $result->getFullNameLocale(),
                    "full_name_english"         => $result->getFullNameEnglish(),
                    "available_regions"         => $regions
                ]
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();

        } catch (\Exception $e) {
            $this->logger->error("Country Info API Error: " . $e->getMessage());

            $responseData = [
                "status"  => false,
                "message" => "Something went wrong.",
                "response" => null
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();
        }
    }
}
