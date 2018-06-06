<?php

namespace What3Words\What3words\Api;

use What3Words\What3Words\Api\Data\QuoteInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface QuoteRepositoryInterface
 * @package What3Words\What3words\Api
 */
interface QuoteRepositoryInterface
{
    /**
     * @param QuoteInterface $quoteItem
     * @return QuoteInterface
     * @throws CouldNotSaveException
     */
    public function save(QuoteInterface $quoteItem);

    /**
     * @param int $quoteItemId
     * @return QuoteInterface
     */
    public function getById($quoteItemId);

    /**
     * @param QuoteInterface $quoteItem
     * @return bool
     */
    public function delete(QuoteInterface $quoteItem);

    /**
     * @return QuoteInterface
     */
    public function create();

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
