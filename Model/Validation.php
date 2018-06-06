<?php

namespace What3Words\What3Words\Model;

/**
 * Class Validation
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class Validation
{
    /**
     * @param string $what3words
     * @param $geocoder \What3Words\Geocoder\Geocoder
     * @param string $iso
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
                'message' => __('Unable to connect to what3words to validate.')
            );
        }
        return $payloadArray;
    }
}
