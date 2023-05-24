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
use Tygh\Tygh;
use Tygh\Addons\SdDepartments\ServiceProvider;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$departments_service = ServiceProvider::getDepartmentsService();

if ($mode === 'view') {
    Tygh::$app['session']['continue_url'] = 'sd_departments.view';

    $params = $_REQUEST;
    $params['items_per_page'] = Registry::get('addons.sd_departments.department_per_page');

    [$departments, $search] = $departments_service->getList($params);

    $supervisor_ids = array_column($departments, 'supervisor_id');

    [$users] = $departments_service->getUsers($params, $supervisor_ids);

    $users_with_keys_as_id = [];

    // Iterate over users array and make
    // new array as [
    //  3 => [
    //     'user_id' => 3,
    //     ...
    //  ]
    //]
    foreach ($users as $user) {
        $users_with_keys_as_id[$user['user_id']] = $user;
    }

    //Add property 'supervisor_info' to departments
    //by getting user info with supervisor id
    foreach ($departments as &$department) {
        $supervisor_id = $department['supervisor_id'];
        $department['supervisor_info'] = $users_with_keys_as_id[$supervisor_id] ?? 'Неизвестный руководитель';
    }

    Tygh::$app['view']->assign([
        'departments' => $departments,
        'search' => $search,
        'columns' => Registry::get('addons.sd_departments.department_columns')
    ]);

    fn_add_breadcrumb(__('sd_departments_departments'));
} elseif ($mode === 'department') {
    $department_id = !empty($_REQUEST['department_id'])
        ? $_REQUEST['department_id']
        : 0;

    $department_data = $departments_service->get($department_id);

    if (empty($department_data) || $department_data['status'] !== ObjectStatuses::ACTIVE) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $params = $_REQUEST;

    $params['items_per_page'] = Registry::get('addons.sd_departments.employee_per_page');

    $employee_ids = !empty($department_data['employee_ids'])
        ? $department_data['employee_ids']
        : [];

    [$users, $search] = $departments_service->getUsers($params, $employee_ids);

    Tygh::$app['view']->assign([
        'department_data' => $department_data,
        'users' => $users,
        'search' => $search,
        'columns' => Registry::get('addons.sd_departments.employee_columns')
    ]);

    fn_add_breadcrumb(__('sd_departments_departments'), 'sd_departments.view');
    fn_add_breadcrumb($department_data['department']);
}
