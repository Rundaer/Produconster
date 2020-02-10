<?php
/** 
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProduconsterClass extends ObjectModel
{
    /** @var integer produconster id*/
    public $id;

    /** @var integer produconster id shop*/
    public $id_shop;

    /** @var int produconster id product*/
    public $id_product;

    /** @var int produconster position*/
    public $position;

    /** @var string produconster description */
    public $description_first;

    /** @var string produconster description */
    public $description_second;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'crea_produconster',
        'primary' => 'id_crea_produconster',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            // Lang fields
            'description_first' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'description_second' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
        )
    );

    public function copyFromPost()
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, $this) and $key != 'id_'.$this->table) {
                $this->{$key} = $value;
            }
        }

        /* Multilingual fields */
        if (sizeof($this->fieldsValidateLang)) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach ($this->fieldsValidateLang as $field => $validation) {
                    if (Tools::getIsset(Tools::getValue($field.'_'.(int)($language['id_lang'])))) {
                        $this->{$field}[(int)($language['id_lang'])] = Tools::getValue($field.'_'.(int)($language['id_lang']));
                    }
                }
            }
        }
    }
}
