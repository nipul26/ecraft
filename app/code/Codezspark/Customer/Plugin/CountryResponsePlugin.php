<?php

namespace Codezspark\Customer\Plugin;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Framework\Webapi\Rest\Response;
use Psr\Log\LoggerInterface;

class CountryResponsePlugin
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

    public function afterGetCountriesInfo(
        CountryInformationAcquirerInterface $subject,
        array $result
    ) {
        try {
            if (empty($result)) {
                $responseData = [
                    "status"  => false,
                    "message" => "Country details could not be fetched.",
                    "response" => null
                ];

                return $this->response->setBody(json_encode($responseData))->sendResponse();
            }

            $countries = [];
            foreach ($result as $country) {
                if (!$country instanceof CountryInformationInterface) {
                    continue;
                }

                $regions = [];
                if ($country->getAvailableRegions()) {
                    foreach ($country->getAvailableRegions() as $region) {
                        $regions[] = [
                            'id'   => $region->getId(),
                            'code' => $region->getCode(),
                            'name' => $region->getName()
                        ];
                    }
                }

                $countries[] = [
                    "id"                        => $country->getId(),
                    "two_letter_abbreviation"   => $country->getTwoLetterAbbreviation(),
                    "three_letter_abbreviation" => $country->getThreeLetterAbbreviation(),
                    "full_name_locale"          => $country->getFullNameLocale(),
                    "full_name_english"         => $country->getFullNameEnglish(),
                    "available_regions"         => $regions
                ];
            }

            $responseData = [
                "status"  => true,
                "message" => "Country details fetched successfully.",
                "response" => $countries
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();

        } catch (\Exception $e) {
            $this->logger->error("Country API Error: " . $e->getMessage());

            $responseData = [
                "status"  => false,
                "message" => "Something went wrong.",
                "response" => null
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();
        }
    }
}
