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
    $suffix = '';
    if ($mode === 'update_department') {
        $department_id = !empty($_REQUEST['department_id'])
            ? $_REQUEST['department_id']
            : 0;

        $data = !empty($_REQUEST['department_data'])
            ? $_REQUEST['department_data']
            : [];

        $res = $departments_service->upsert($data, $department_id);

        if ($res === false) {
            fn_set_notification(
                NotificationSeverity::ERROR,
                __('error'),
                __('text_fill_the_mandatory_fields')
            );

            $suffix .= empty($department_id)
                ? '.add_department'
                : '.update_department&department_id=' . $department_id;
        } else {
            $suffix .= '.manage_departments';
        }
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
    }

    if ($mode === 'departments_update_statuses') {
        $status_to = $_REQUEST['status'] ?? '';
        $department_ids = $_REQUEST['departments_ids'] ?? [];

        $result = $departments_service->updateStatuses($status_to, $department_ids);

        if ($result) {
            fn_set_notification(
                NotificationSeverity::NOTICE,
                __('notice'),
                __('status_changed')
            );
        } else {
            fn_set_notification(
                NotificationSeverity::ERROR,
                __('error'),
                __('error_status_not_changed')
            );
        }

        $redirect_url = $_REQUEST['redirect_url'] ?? 'profiles.manage_departments';

        if (defined('AJAX_REQUEST')) {
            Tygh::$app['ajax']->assign('force_redirection', $redirect_url);
            Tygh::$app['ajax']->assign('non_ajax_notifications', true);

            return [CONTROLLER_STATUS_NO_CONTENT];
        }

        return [CONTROLLER_STATUS_OK, $redirect_url];
    }

    if ($mode === 'departments_store_selection') {
        if (!empty($_REQUEST['departments_ids']) && !empty($_REQUEST['selected_fields'])) {
            Tygh::$app['session']['departments_ids'] = $_REQUEST['departments_ids'];
            Tygh::$app['session']['selected_fields'] = $_REQUEST['selected_fields'];

            unset($_REQUEST['redirect_url']);
            $suffix = '.m_update_departments';
        } else {
            $suffix = '.manage_departments';
        }
    }

    if ($mode === 'm_update_departments') {
        if (empty($_REQUEST['department_data'])) {
            return [CONTROLLER_STATUS_OK];
        }

        $departments_service->updateMultiple($_REQUEST['department_data']);

        unset(Tygh::$app['session']['departments_ids']);
        unset(Tygh::$app['session']['selected_fields']);
    }

    return [CONTROLLER_STATUS_OK, 'profiles' . ($suffix ?: '.manage_departments')];
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
} elseif ($mode === 'm_update_departments') {
    $departments_ids = Tygh::$app['session']['departments_ids'];
    $selected_fields = Tygh::$app['session']['selected_fields'];
    $is_empty_session_params = empty($departments_ids) || empty($selected_fields);
    $is_object_not_department = empty($selected_fields['object']) || $selected_fields['object'] !== 'department';

    if ($is_empty_session_params || $is_object_not_department) {
        return [CONTROLLER_STATUS_REDIRECT, 'profiles.manage_departments'];
    }

    $fields2update = $selected_fields['data'];

    $data_search_fields = implode(', ', $fields2update);

    if (!empty($data_search_fields)) {
        $data_search_fields = ', ' . $data_search_fields;
    }

    $field_names = [];
    foreach ($fields2update as $field) {
        switch ($field) {
            case 'supervisor_id':
                $desc = 'sd_departments_supervisor';
                break;
            case 'employee_ids':
                $desc = 'sd_departments_employees';
                break;
            case 'department':
                $desc = 'name';
                break;
        }

        $field_names[$field] = __($desc);
    }

    $params = [];
    $params['item_ids'] = $departments_ids;
    $params['employees'] = true;

    [$department_data] = $departments_service->getList($params);

    $departments_service->setSupervisorInfo($department_data);

    Tygh::$app['view']->assign([
        'fields2update' => $fields2update,
        'field_names' => $field_names,
        'department_data' => $department_data
    ]);
}
