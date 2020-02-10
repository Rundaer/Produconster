{**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<style>
    #produconster{
        width: 100%;
        margin: 20px 0;
        padding: 5px;
        border: 1px solid #bbcdd2;
    }
    #produconster img{
        max-width: 100%;
        height: auto;
    }
    #produconster .header{
        height: 50px;
        padding: 5px 10px;
        display: flex;
        align-items: center;
    }
    #produconster .header a{
        color: #25b9d7;
        background-color: transparent;
        background-image: none;
        border-color: #25b9d7;
        padding: 10px 30px;
        border: 1px solid;
    }
    #produconster #blocks{
        display: grid;
        padding: 20px;
        row-gap: 10px;
    }
    #produconster #blocks .item{
        display: flex;
        flex: 1 1 50%;
        border: 1px solid #999;
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        position: relative
    }
    #produconster #blocks .item:hover{
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    }
    #produconster #blocks .item.ui-sortable-helper{
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    }
    #produconster #blocks .item > div {
        flex: 1;
        padding: 20px
    }

    #produconster #blocks .item .actions{
        position: absolute;
        right: 0;
        padding: 5px;
        background-color: rgba(0,0,0,.1);
    }
</style>

<div id='produconster'>
    <div class="header">
        <nav>
            <a href="{$link->getAdminLink('AdminModules')}&configure=crea_produconster&addBlock=1&id_product={$id_product}">Dodaj nowy block</a>
        <nav>
    </div>
    <div class="content">
        <div id="blocks">
            {foreach from=$items item=item key=key name=name}
                {* {$item|print_r} *}
                <div id="blocks_{$item.id_crea_produconster}" class="item">
                    <div class="text">
                        {$item.description_first}
                    </div>
                    {if $item.description_second}
                        <div class="text">
                            {$item.description_second}
                        </div>
                    {/if}
                    {block name="actions"}
                        {include file="{$module_templates}hook/actions.tpl" id=$item.id_crea_produconster}
                    {/block}
                </div>
            {/foreach}
        </div>
    </div>
</div>