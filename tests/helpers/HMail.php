<?php
namespace batsg\helpers;



/**
 * Helper class for Mail.
 */
class HMail
{

    /**
     * Split mail addresses string and return as array.
     * @param string $addresses
     * @return string[]
     */
    public static function getMailAddressAsArray($addresses)
    {
        return explode(',', $addresses);
    }
}