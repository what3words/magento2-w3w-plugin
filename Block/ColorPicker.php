<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */
namespace What3Words\What3Words\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ColorPicker
 * Create colorpicker element for admin config field
 */
class ColorPicker extends Field
{

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
                    require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                        $(document).ready(function () {
                            var $element = $("#' . $element->getHtmlId() . '");
                            $element.css("backgroundColor", "' . $value . '");
                
                            // Attach the color picker
                            $element.ColorPicker({
                                color: "' . $value . '",
                                onChange: function (hsb, hex, rgb) {
                                    $element.css("backgroundColor", "#" + hex).val("#" + hex);
                                }
                            });
                        });
                    });
                </script>';

        return $html;
    }
}
