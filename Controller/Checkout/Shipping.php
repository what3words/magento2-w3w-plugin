<?php

namespace What3Words\What3Words\Controller\Checkout;

/** What3Words extension classes */
use What3Words\What3Words\Model\QuoteFactory;
use What3Words\What3Words\Helper\Data;

/** Magento classes */
use \Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class Shipping
 * @package What3Words\What3Words\Controller\Checkout
 * @author Vicki Tingle
 */
class Shipping extends Action
{
    /** @var ResultFactory  */
    protected $resultFactory;

    /** @var QuoteFactory  */
    protected $w3wQuoteFactory;

    /** @var CheckoutSession  */
    protected $checkoutSession;

    /** @var Data  */
    protected $w3wHelper;

    /**
     * Shipping constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param QuoteFactory $w3wQuoteFactory
     * @param CheckoutSession $checkoutSession
     * @param Data $w3wHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        QuoteFactory $w3wQuoteFactory,
        CheckoutSession $checkoutSession,
        Data $w3wHelper
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultPageFactory;
        $this->w3wQuoteFactory = $w3wQuoteFactory;
        $this->checkoutSession = $checkoutSession;
        $this->w3wHelper = $w3wHelper;
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * Save the three word address from the checkout to the quote table
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $what3words = $this->getRequest()->getParam('what3words');
        $saveInAddressBook = $this->getRequest()->getParam('saveInAddressBook');
        $customerAddressId = $this->getRequest()->getParam('customerAddressId');
        $quoteId = $this->checkoutSession->getQuoteId();

        if (isset($what3words) && $what3words !== '') {
            $shouldSave = $this->w3wHelper->shouldSaveQuote($quoteId, $what3words);
            // Only save this if either there is no entry for this quote or if the what3words has changed
            if ($shouldSave['save']) {
                // Update if what3words has changed
                if ($shouldSave['replace']) {
                    $this->w3wHelper->updateExistingQuote($quoteId, $what3words);
                } else {
                    $what3wordsQuote = $this->w3wQuoteFactory->create();
                    $what3wordsQuote->setQuoteId($quoteId);
                    $what3wordsQuote->setW3w($what3words);
                    if ($saveInAddressBook) {
                        $what3wordsQuote->setAddressFlag(true);
                    }
                    try {
                        $what3wordsQuote->save();
                        $resultJson->setData(array('success' => true, 'what3words' => $what3words));
                        return $resultJson;
                    } catch (\Exception $e) {
                        $resultJson->setData(array('success' => false, 'message' => 'Could not save what3words.'));
                        return $resultJson;
                    }
                }
            }
            $resultJson->setData(array('success' => true, 'message' => 'Item already exists'));
            return $resultJson;
        } elseif (isset($customerAddressId)) {
            /** @var \What3Words\What3Words\Model\Address $addressItem */
            $addressItem = $this->w3wHelper->getW3wItemByAddressId($customerAddressId);
            $what3wordsQuote = $this->w3wQuoteFactory->create();
            $what3wordsQuote->setQuoteId($quoteId);
            $what3wordsQuote->setW3w($addressItem->getW3w());

            try {
                $what3wordsQuote->save();
                $resultJson->setData(array('success' => true, 'what3words' => $what3words));
                return $resultJson;
            } catch (\Exception $e) {
                $resultJson->setData(array('success' => false, 'message' => 'Could not save 3 word address.'));
                return $resultJson;
            }
        }
        $resultJson->setData(array('success' => true, 'message' => 'No 3 word address used.'));
        return $resultJson;
    }
}
