<?php

namespace What3Words\What3Words\Controller\Checkout;

/** What3Words PHP Wrapper class */
use \What3words\Geocoder\Geocoder;

/** What3Words extension classes */
use What3Words\What3Words\Helper\Config as ConfigHelper;
use What3Words\What3Words\Model\Validation as Validator;

/** Magento classes */
use \Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Validation
 * @package What3Words\What3Words\Controller\Checkout
 * @author Vicki Tingle
 */
class Validation extends Action
{
    /** @var ResultFactory  */
    protected $resultFactory;

    /** @var PageFactory  */
    protected $pageFactory;

    /** @var ConfigHelper  */
    protected $configHelper;

    /** @var Validator  */
    protected $validation;

    /**
     * Validation constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param ConfigHelper $configHelper
     * @param Validator $validation
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ConfigHelper $configHelper,
        Validator $validation
    ) {
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
        $this->pageFactory = $pageFactory;
        $this->configHelper = $configHelper;
        $this->validation = $validation;
    }

    /**
     * Validate the request and return specific error
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $what3Words = $this->getRequest()->getParam('what3words');
        $isoCode = $this->getRequest()->getParam('iso');
        // Make sure we have an API key
        $apiKey = $this->configHelper->getApiKey();
        if ($apiKey) {
            $options = array(
                'key' => $apiKey,
                'timeout' => 10
            );
            $geocoder = new Geocoder($options);

            $payload = $geocoder->forwardGeocode($what3Words);
            $countryPayload = $this->validateCountry($what3Words, $geocoder, $isoCode);

            if (isset($countryPayload['what3words']) && !$countryPayload['what3words']) {
                $resultJson->setData(
                    array(
                        'success' => false,
                        'country' => false,
                        'message' => $countryPayload['message']
                    )
                );
                return $resultJson;
            }

            //Analyse the payload
            $payloadArr = json_decode($payload, true);

            // If crs is set, it means we have a valid location
            if (isset($payloadArr['crs'])) {
                $resultJson->setData(
                    array(
                        'success' => true,
                        'payload' => $payloadArr
                    )
                );
                return $resultJson;
            }

            if (isset($payloadArr['status'])) {
                $message = isset($payloadArr['status']['message']) ?
                    $payloadArr['status']['message'] :
                    'Please re-enter or edit your 3 word address, and select the correct one when the AutoSuggest list displays';
                //Otherwise send the error message through
                $resultJson->setData(
                    array(
                        'success' => false,
                        'invalid' => true,
                        'message' => __($message)
                    )
                );
                return $resultJson;
            } else {
                $resultJson->setData(
                    array(
                        'success' => false,
                        'invalid' => true,
                        'message' => __('An error occurred while validating the chosen what3words.')
                    )
                );
                return $resultJson;
            }
        }

        return $resultJson;
    }

    /**
     * Check that the selected 3 word address is in the correct country scope
     * @param $what3words string
     * @param $geocoder Geocoder
     * @param $iso string
     * @return array|mixed
     */
    public function validateCountry($what3words, $geocoder, $iso)
    {
        try {
            $payload = $geocoder->autoSuggest($what3words);
            $payloadArray = $payloadArr = json_decode($payload, true);

            $returnedWords = $payloadArray['suggestions'][0]['words'];
            $matchedIso = $payloadArray['suggestions'][0]['country'];

            if ($returnedWords === $what3words) {
                if ($matchedIso === strtolower($iso)) {
                    return array(
                        'what3words' => true,
                        'message' => $payloadArray
                    );
                } else {
                    return array(
                        'what3words' => false,
                        'message' => __(
                            'The 3 word address you have entered is not in the country selected above. Please re-enter or edit your 3 word address, or change the country.'
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            return array(
                'what3words' => false,
                'message' => __('Please re-enter or edit your 3 word address, and select the correct one when the AutoSuggest list displays.')
            );
        }
        return $payloadArray;
    }
}
