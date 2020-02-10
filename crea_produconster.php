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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once _PS_MODULE_DIR_.'crea_produconster/produconsterClass.php';

class Crea_produconster extends Module
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'crea_produconster';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';                       
        $this->author = 'Konrad Babiarz';  
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);

        parent::__construct();

        $this->displayName = $this->l('Custom crea module xtra description');
        $this->description = $this->l('Module to display extra description allegro alike');
        $this->ps_versions_compliancy = array('min' => '1.7.6.0', 'max' => _PS_VERSION_);
    }

    /**
     * Instalator
     * 
     * @see Module::install()
     * @return bool
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install()
            && $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
            && $this->registerHook('actionProductSave')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    /**
     * Uninstall
     * 
     * @see Module::uninstall()
     * @return bool
     */
    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');
        return parent::uninstall(); 
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        // Only on product page
        if ('product' === $this->context->controller->php_self) {
            $this->context->controller->registerStylesheet(
                'produconster-style',
                'modules/'.$this->name.'/views/css/front/produconster.css',
                [
                  'media' => 'all',
                  'priority' => 200,
                ]
            );
        }
    }

    /**
     * Hook to product admin page
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $id_lang = $this->context->language->id;
        $items = $this->getData($id_lang, $params['id_product']);

        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'id_product' => $params['id_product'],
                'items' => $items,
                'module_templates' => dirname(__FILE__).'/views/templates/'
            )
        );

        return $this->display(__FILE__, 'views/templates/hook/catalog.tpl').$this->dragdropScript();
    }

    /**
     * Hook to product front page
     */
    public function hookDisplayFooterProduct($params)
    {
        if ('product' === $this->context->controller->php_self){
            $id_lang = $this->context->language->id;
            $items = $this->getData($id_lang, $params['product']['id_product']);
    
            $this->context->smarty->assign(
                array(
                    'items' => $items,
                )
            );
    
            return $this->display(__FILE__, 'views/templates/hook/product_extra_desc.tpl');
        }
    }

    /**
     * Module method
     */
    public function getContent()
    {
        $id_crea_produconster = (int)Tools::getValue('id_crea_produconster');
        $html = '';

        // If submit adding
        if (Tools::isSubmit('saveBlock')) {
            if ($id_crea_produconster = Tools::getValue('id_crea_produconster')) {
                $produconster = new produconsterClass((int)$id_crea_produconster);
            } else {
                $produconster = new produconsterClass();
            }

            $produconster->copyFromPost();
            $produconster->position = 0;
            $produconster->id_shop = $this->context->shop->id;

            if ($produconster->validateFields(false) && $produconster->validateFieldsLang(false)) {
                $produconster->save();
                $this->_clearCache('*');
            } else {
                $html .= '<div class="conf error">'.$this->l('An error occurred while attempting to save.').'</div>';
            }
            return $html.Tools::redirectAdmin('index.php/sell/catalog/products/'.(int)($produconster->id_product).'?_token='.Tools::getAdminToken('AdminProducts').'#tab-step1');
        }

        // If add new block
        if(Tools::isSubmit('addBlock') || Tools::isSubmit('updateBlock') )
        {
            $helper = $this->initForm();
            foreach (Language::getLanguages(false) as $lang) {
                if ($id_crea_produconster) {
                    $crea_produconster = new produconsterClass((int)$id_crea_produconster);
                    $helper->fields_value['description_first'][(int)$lang['id_lang']] = $crea_produconster->description_first[(int)$lang['id_lang']];
                    $helper->fields_value['description_second'][(int)$lang['id_lang']] = $crea_produconster->description_second[(int)$lang['id_lang']];
                    $helper->fields_value['id_product'] = $crea_produconster->id_product;
                } else {
                    $helper->fields_value['description_first'][(int)$lang['id_lang']] = Tools::getValue('description_first_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['description_second'][(int)$lang['id_lang']] = Tools::getValue('description_second_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['id_product'] = Tools::getValue('id_product', '');
                    $helper->fields_value['position'] = Tools::getValue('position', '');
                }
            }

            if ($id_crea_produconster = Tools::getValue('id_crea_produconster')) {
                $this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_crea_produconster');
                $helper->fields_value['id_crea_produconster'] = (int)$id_crea_produconster;
            }

            return $helper->generateForm($this->fields_form);
        }

        // If delete block
        if(Tools::isSubmit('deleteBlock'))
        {
            $produconster = new produconsterClass((int)Tools::getValue('id_product'));
            $productid = $produconster->id_product;

            $produconster->delete();
            $this->_clearCache('*');

            return $html.Tools::redirectAdmin('index.php/sell/catalog/products/'.$productid.'?_token='.Tools::getValue('token').'#tab-step1');
        }
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function initForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('New Block'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Product Id'),
                    'name' => 'id_product',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Product details column 1'),
                    'name' => 'description_first',
                    'lang' => true,
                    'autoload_rte' => true,
                    'hint' => $this->l('For example... add new image.'),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Product details column 1'),
                    'name' => 'description_second',
                    'lang' => true,
                    'autoload_rte' => true,
                    'hint' => $this->l('For example... add description.'),
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'saveBlock';
        return $helper;
    }

    protected function dragdropScript()
    {
        $this->context->controller->addJqueryUI('ui.sortable');
        /* Style & js for fieldset 'slides configuration' */
        $html = '<script type="text/javascript">
            $(function() {
                var $myBlocks = $("#blocks");
                $myBlocks.sortable({
                    opacity: 0.6,
                    cursor: "move",
                    update: function() {
                        var order = $(this).sortable("serialize") + "&action=updateBlockPosition";
                        $.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
                        console.log(123);
                        }
                    });
                $myBlocks.hover(function() {
                    $(this).css("cursor","move");
                    },
                    function() {
                    $(this).css("cursor","auto");
                });
            });
        </script>';

        return $html;
    }

    protected function getData($id_lang, $id_product)
    {
        $items =  Db::getInstance()->executeS('
        SELECT r.`id_crea_produconster`, r.`id_shop`, r.`id_product`, rl.`description_first`, rl.`description_second`
        FROM `'._DB_PREFIX_.'crea_produconster` r
        LEFT JOIN `'._DB_PREFIX_.'crea_produconster_lang` rl ON (r.`id_crea_produconster` = rl.`id_crea_produconster`)
        WHERE `id_lang` = '.(int)$id_lang.' && `id_product` = ' .(int)$id_product . Shop::addSqlRestrictionOnLang() .'
        ORDER BY r.`position`');

        return $items;
    }
}