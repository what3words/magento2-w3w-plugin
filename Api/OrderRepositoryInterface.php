<?php

namespace What3Words\What3words\Api;

use What3Words\What3Words\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface OrderRepositoryInterface
 * @package What3Words\What3words\Api
 */
interface OrderRepositoryInterface
{
    /**
     * @param OrderInterface $orderItem
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $orderItem);

    /**
     * @param int $orderItemId
     * @return OrderInterface
     */
    public function getById($orderItemId);

    /**
     * @param OrderInterface $orderItem
     * @return bool
     */
    public function delete(OrderInterface $orderItem);

    /**
     * @return OrderInterface
     */
    public function create();

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
