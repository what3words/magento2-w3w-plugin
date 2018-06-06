<?php

namespace What3Words\What3Words\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface AttributeInterface extends ExtensibleDataInterface
{
    const THREE_WORD_ADDRESS = 'w3w';

    public function getW3w();

    public function setW3w($w3w);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \What3Words\What3Words\Api\Data\AttributeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param AttributeExtensionInterface $extensionAttributes
     * @return mixed
     */
    public function setExtensionAttributes(\What3Words\What3Words\Api\Data\AttributeExtensionInterface $extensionAttributes);
}
