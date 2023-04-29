<?php

/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($mode === 'view') {

    Tygh::$app['session']['continue_url'] = 'departments.view';

    $params = $_REQUEST;

    $params['status'] = ObjectStatuses::ACTIVE;

    list($departments, $search) = fn_get_departments(
        $params,
        Registry::get('settings.Appearance.products_per_page'),
        CART_LANGUAGE
    );

    Tygh::$app['view']->assign('departments', $departments);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign(
        'columns',
        Registry::get('settings.Appearance.columns_in_products_list')
    );

    fn_add_breadcrumb(__('departments'));
} elseif ($mode === 'department') {

    $department_id = !empty($_REQUEST['department_id'])
        ? $_REQUEST['department_id']
        : 0;

    $department_data = fn_get_department_data($department_id, CART_LANGUAGE);

    if (empty($department_data) || $department_data['status'] !== ObjectStatuses::ACTIVE) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    Tygh::$app['view']->assign('department_data', $department_data);

    fn_add_breadcrumb(__('departments'), 'departments.view');
    fn_add_breadcrumb($department_data['department']);

    $params = $_REQUEST;
    $params['extend'] = ['description'];
    $params['user_id'] = !empty($department_data['employee_ids'])
        ? $department_data['employee_ids']
        : -1;

    $params['items_per_page'] = Registry::get('settings.Appearance.products_per_page');

    list($users, $search) = fn_get_users($params, Tygh::$app["session"]['auth']);
    Tygh::$app['view']->assign('users', $users);
    Tygh::$app['view']->assign('search', $search);
}
