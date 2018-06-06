<?php

namespace What3Words\What3Words\Controller\Adminhtml\Customer;

use \Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use What3Words\What3Words\Model\AddressRepository;

/**
 * Class Ajax
 * @package What3Words\What3Words\Controller\Adminhtml\Customer
 * @author Vicki Tingle
 */
class Ajax extends Action
{
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    protected $addressRepository;

    /**
     * Info constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param AddressRepository $addressRepository
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        AddressRepository $addressRepository
    ) {
        parent::__construct($context);
        $this->addressRepository = $addressRepository;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $addressId = $this->getRequest()->getParam('addressId');

        if (isset($addressId)) {
            $items = $this->addressRepository->getW3wByAddressId($addressId);
            if ($items) {
                /** @var \What3Words\What3Words\Model\Address $item */
                foreach ($items as $item) {
                    if ($item->getW3w()) {
                        $resultJson->setData(
                            array(
                                'success' => true,
                                'w3w' => $item->getW3w()
                            )
                        );
                        return $resultJson;
                    }
                }
            }
            $resultJson->setData(
                array(
                    'success' => false
                )
            );

        }
        return $resultJson;
    }
}
