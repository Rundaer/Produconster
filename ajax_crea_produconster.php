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

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('crea_produconster.php');

$produconster = new ProduconsterClass();
$blocks = array();

if (Tools::getValue('action') == 'updateBlockPosition' && Tools::getValue('blocks') ){
    $blocks = Tools::getValue('blocks');
    foreach ($blocks as $position => $id_slide){
        Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'crea_produconster` SET `position` = '.(int)$position.'
			WHERE `id_crea_produconster` = '.(int)$id_slide
        );
    }
    $produconster->clearCache();
}
