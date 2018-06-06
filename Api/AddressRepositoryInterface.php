<?php

namespace What3Words\What3words\Api;

use What3Words\What3Words\Api\Data\AddressInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface AddressRepositoryInterface
 * @package What3Words\What3words\Api
 */
interface AddressRepositoryInterface
{
    /**
     * @param AddressInterface $addressItem
     * @return AddressInterface
     * @throws CouldNotSaveException
     */
    public function save(AddressInterface $addressItem);

    /**
     * @param int $addressItemId
     * @return AddressInterface
     */
    public function getById($addressItemId);

    /**
     * @param AddressInterface $addressItem
     * @return bool
     */
    public function delete(AddressInterface $addressItem);

    /**
     * @return AddressInterface
     */
    public function create();

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
