<?php
namespace batsg\helpers;

/**
 * Manipulate address of japan.
 */
class HJapaneseAddress
{
    const ADDRESS_PART_TODOFUKEN = 1; // 都道府県
    const ADDRESS_PART_SHICHOUSON = 2; // 市町村
    const ADDRESS_PART_AZAMEI = 3; // 字名
    const ADDRESS_PART_BANCHI = 4; // 番地
    const ADDRESS_PART_JUUKYOHYOUJI = 5; // 住居表示

    // Reference: http://qiita.com/zakuroishikuro/items/066421bce820e3c73ce9
    const SPLIT_PATTERN_TODOFUKEN = '((?:...??[都道府県])?)';
    const SPLIT_PATTERN_SHICHOUSON = '((?:(?:旭川|伊達|石狩|盛岡|奥州|田村|南相馬|那須塩原|東村山|武蔵村山|羽村|十日町|上越|富山|野々市|大町|蒲郡|四日市|姫路|大和郡山|廿日市|下松|岩国|田川|大村)市|.+?郡(?:玉村|大町|.+?)[町村]|.+?市.+?区|.+?[市区町村])?)';
    const SPLIT_PATTERN_AZAMEI = '([^0-9-]*)';
    const SPLIT_PATTERN_BANCHI = '([0-9-]*)';
    const SPLIT_PATTERN_JUUKYOHYOUJI = '(.*)';
    
    /**
     * Split a address into part.
     * @param string $address The address to be splitted.
     * @return string[] An array with the keys are ADDRESS_PART_XXX, and the values are the appropriate part in the address. 
     */
    public static function splitAddress($address)
    {
        // Convert digits to half width.
        $address = HJapanese::fullWidthToHalfWidth($address);
        $pattern = self::SPLIT_PATTERN_TODOFUKEN . self::SPLIT_PATTERN_SHICHOUSON . self::SPLIT_PATTERN_AZAMEI . self::SPLIT_PATTERN_BANCHI . self::SPLIT_PATTERN_JUUKYOHYOUJI;
        if (!preg_match("/$pattern/u", $address, $matches)) {
		    throw new Exception("Split address failed $address");
		};
        unset($matches[0]);
		return $matches;
    }
}
?>