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

use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Addons\SdDepartments\ServiceProvider;
use Tygh\Enum\NotificationSeverity;

defined('BOOTSTRAP') or die('Access denied');

$departments_service = ServiceProvider::getDepartmentsService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update_department') {
        $department_id = !empty($_REQUEST['department_id'])
            ? $_REQUEST['department_id']
            : 0;

        $data = !empty($_REQUEST['department_data'])
            ? $_REQUEST['department_data']
            : [];

        $res = $departments_service->upsert($data, $department_id);

        $url = 'profiles.';

        if ($res === false) {
            fn_set_notification(
                NotificationSeverity::ERROR,
                __('error'),
                __('text_fill_the_mandatory_fields')
            );

            $url .= empty($department_id)
                ? 'add_department'
                : "update_department&department_id=$department_id";
        } else {
            $url .= 'manage_departments';
        }

        return [CONTROLLER_STATUS_OK, $url];
    }

    if ($mode === 'delete_department') {
        $department_id = !empty($_REQUEST['department_id'])
            ? $_REQUEST['department_id']
            : 0;

        $departments_service->delete($department_id);

        fn_set_notification(
            NotificationSeverity::NOTICE,
            __('notice'),
            __('sd_departments_success_delete_one')
        );

        return [CONTROLLER_STATUS_OK, 'profiles.manage_departments'];
    }

    if ($mode === 'delete_departments') {
        if (!empty($_REQUEST['departments_ids'])) {
            $departments_service->delete($_REQUEST['departments_ids']);
            fn_set_notification(
                NotificationSeverity::NOTICE,
                __('notice'),
                __('sd_departments_success_delete_multiple')
            );
        }
        return [CONTROLLER_STATUS_OK, 'profiles.manage_departments'];
    }
}

if ($mode === 'update_department' || $mode === 'add_department') {
    $department_id = !empty($_REQUEST['department_id'])
        ? $_REQUEST['department_id']
        : 0;

    $department_data = $departments_service->get($department_id);

    if (empty($department_data) && $mode === 'update_department') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    Tygh::$app['view']->assign('department_data', $department_data);
} elseif ($mode === 'manage_departments') {
    $params = $_REQUEST;
    $params['items_per_page'] = $_REQUEST['items_per_page'] ??
        Registry::get('settings.Appearance.admin_elements_per_page');

    [$departments, $search] = $departments_service->getList($params);

    Tygh::$app['view']->assign([
        'departments' => $departments,
        'search' => $search
    ]);
}
