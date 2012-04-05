<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2012 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin:
 * This is a Smarty plugin to handle the creation of HTML List elements for Sugar Action Menus.
 * Based on the theme, the plugin generates a proper group of button lists.
 *
 * @param $params array - its structure is
 *     'buttons' => list of button htmls, such as ( html_element1, html_element2, ..., html_element_n),
 *     'id' => id property for ul element
 *     'class' => class property for ul element
 * @param $smarty
 *
 * @return string - compatible sugarActionMenu structure, such as
 * <ul>
 *     <li>html_element1
 *         <ul>
 *              <li>html_element2</li>
 *                  ...
 *              </li>html_element_n</li>
 *         </ul>
 *     </li>
 * </ul>
 * ,which is generated by @see function smarty_function_sugar_menu
 *
 * <pre>
 * 1. SugarButton on smarty
 *
 * add appendTo to generate button lists
 * {{sugar_button ... appendTo='buttons'}}
 *
 * ,and then create menu
 * {{sugar_action_menu ... buttons=$buttons ...}}
 *
 * 2. Code generate in PHP
 * <?php
 * ...
 *
 * $buttons = array(
 *      '<input ...',
 *      '<a href ...',
 *      ...
 * );
 * require_once('include/Smarty/plugins/function.sugar_action_menu.php');
 * $action_button = smarty_function_sugar_action_menu(array(
 *     'id' => ...,
 *     'buttons' => $buttons,
 *     ...
 * ),$xtpl);
 * $template->assign("ACTION_BUTTON", $action_button);
 * ?>
 * 3. Passing array to smarty in PHP
 * $action_button = array(
 *      'id' => 'id',
 *      'buttons' => array(
 *          '<input ...',
 *          '<a href ...',
 *          ...
 *      ),
 *      ...
 * );
 * $tpl->assign('action_button', $action_button);
 * in the template file
 * {sugar_action_menu params=$action_button}
 *
 * 4. Append button element in the Smarty
 * {php}
 * $this->append('buttons', "<a ...");
 * $this->append('buttons', "<input ...");
 * {/php}
 * {{sugar_action_menu ... buttons=$buttons ...}}
 * </pre>
 *
 * @author Justin Park (jpark@sugarcrm.com)
 */
function smarty_function_sugar_action_menu($params, &$smarty)
{
    $theme = !empty($params['theme']) ? $params['theme'] : SugarThemeRegistry::current()->name;

    if( !empty($params['params']) ) {
        $addition_params = $params['params'];
        unset($params['params']);
        $params = array_merge_recursive($params, $addition_params);
    }
    //if buttons have not implemented, it returns empty string;
    if(empty($params['buttons']))
        return '';

    if(is_array($params['buttons']) && $theme != 'Classic') {

        $menus = array(
            'html' => array_shift($params['buttons']),
            'items' => array()
        );

        foreach($params['buttons'] as $item) {
            if(strlen($item)) {
                array_push($menus['items'],array(
                   'html' => $item
               ));
            }
        }
        $action_menu = array(
            'id' => !empty($params['id']) ? (is_array($params['id']) ? $params['id'][0] : $params['id']) : '',
            'htmlOptions' => array(
                'class' => !empty($params['class']) && strpos($params['class'], 'clickMenu') !== false  ? $params['class'] : 'clickMenu '. (!empty($params['class']) ? $params['class'] : ''),
                'name' => !empty($params['name']) ? $params['name'] : '',
            ),
            'itemOptions' => array(
                'class' => (count($menus['items']) == 0) ? 'single' : 'sugar_action_button'
            ),
            'submenuHtmlOptions' => array(
                'class' => 'subnav'
            ),
            'items' => array(
                $menus
            )
        );
        require_once('function.sugar_menu.php');
        return smarty_function_sugar_menu($action_menu, $smarty);

    }

    if (is_array($params['buttons'])) {
        return '<div class="action_buttons">' . implode(' ', $params['buttons']).'</div>';
    } else if(is_array($params)) {
        return '<div class="action_buttons">' . implode(' ', $params).'</div>';
    }

    return $params['buttons'];
}

?>