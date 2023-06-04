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

namespace Tygh\Addons\SdDepartments\Users;

use Tygh\Application;
use Tygh\Database\Connection;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\MultiQueryTypes;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\SiteArea;
use Tygh\Languages\Languages;
use Tygh\Registry;
use Tygh\Tygh;

/**
 * Class Users Departments
 *
 * @package Departments
 */
class Departments
{
    protected Connection $db;

    protected string $area;

    protected string $lang_code;

    /**
     * Departments constructor.
     *
     * @param Application $app
     * @param string $area
     * @param string $lang_code
     */
    public function __construct(Application $app, $area = AREA)
    {
        $this->db = $app['db'];
        $this->area = $area;
        $this->lang_code = AREA === SiteArea::STOREFRONT ? CART_LANGUAGE : DESCR_SL;
    }

    /**
     * Get all departments matching parameters
     *
     * @param array<mixed> $params query params
     *
     * @return array Department data
     */
    public function getList($params)
    {
        // Set default values to input params
        $default_params = [
            'page' => 1,
            'items_per_page' => 0
        ];

        $params = array_merge($default_params, $params);

        /**
         * Executes at the beginning of the method, allowing you to modify the arguments passed to the method.
         *
         * @param string[] $params Params
         */
        fn_set_hook('get_departments_pre', $params);

        $cache_key = __FUNCTION__ . md5(serialize($params));

        Registry::registerCache(
            $cache_key,
            ['departments', 'department_descriptions'],
            Registry::cacheLevel('locale_auth'),
            true
        );


        $cache = Registry::get($cache_key);

        $fields = [
            'id' => '?:departments.department_id',
            'status' => '?:departments.status',
            'timestamp' => '?:departments.timestamp',
            'supervisor_id' => '?:departments.supervisor_id',
            'name' => '?:department_descriptions.department',
            'description' => '?:department_descriptions.description'
        ];

        $sortings = [
            'timestamp' => '?:departments.timestamp',
            'name' => '?:department_descriptions.department',
            'status' => '?:departments.status',
        ];

        $condition = [];
        $limit = '';
        $join = '';

        if (!empty($params['limit'])) {
            $limit = $this->db->quote(' LIMIT 0, ?i', $params['limit']);
        }

        if (!empty($params['item_ids'])) {
            $condition['item_ids'] = $this->db->quote(
                'AND ?:departments.department_id IN (?n)',
                $params['item_ids']
            );
        }

        if (!empty($params['department_id'])) {
            $condition['department_id'] = $this->db->quote(
                'AND ?:departments.department_id = ?i',
                $params['department_id']
            );
        }

        if ($this->area === SiteArea::STOREFRONT) {
            $condition['status'] = $this->db->quote(
                'AND ?:departments.status = ?s',
                ObjectStatuses::ACTIVE
            );
        } elseif (!empty($params['status'])) {
            $condition['status'] = $this->db->quote(
                'AND ?:departments.status = ?s',
                $params['status']
            );
        }

        if (!empty($params['department_name'])) {
            $condition['department_name'] = $this->db->quote(
                'AND ?:department_descriptions.department LIKE ?l',
                '%' . $params['department_name'] . '%'
            );
        }

        if (!empty($params['supervisor_id'])) {
            $condition['supervisor_id'] = $this->db->quote(
                'AND ?:departments.supervisor_id = ?s',
                $params['supervisor_id']
            );
        }

        $join .= $this->db->quote(
            ' LEFT JOIN ?:department_descriptions' .
                ' ON ?:department_descriptions.department_id = ?:departments.department_id' .
                ' AND ?:department_descriptions.lang_code = ?s',
            $this->lang_code
        );

        if (!empty($params['items_per_page'])) {
            $params['total_items'] = $this->db->getField(
                'SELECT COUNT(*) FROM ?:departments ?p WHERE 1 ?p',
                $join,
                implode(' ', $condition)
            );

            $limit = db_paginate(
                $params['page'],
                $params['items_per_page'],
                $params['total_items']
            );
        }

        if (!empty($cache)) {
            $departments = $cache;
        } else {
            /**
             * Prepare params for getting departments query
             *
             * @param string[] $params Params
             * @param string[] $fields Query fields
             * @param string[] $condition Query conditions
             * @param string[] $sortings Query sortings params
             */
            fn_set_hook('get_departments', $params, $fields, $condition, $sortings);

            $sorting = db_sort($params, $sortings, 'name', 'asc');

            $departments = $this->db->getHash(
                'SELECT ?p FROM ?:departments ' .
                    $join .
                    'WHERE 1 ?p ?p ?p',
                'department_id',
                implode(', ', $fields),
                implode(' ', $condition),
                $sorting,
                $limit
            );

            if (!empty($departments)) {
                Registry::set($cache_key, $departments);
            }
        }

        $department_image_ids = array_keys($departments);
        $images = fn_get_image_pairs(
            $department_image_ids,
            'department',
            ImagePairTypes::MAIN,
            true,
            false,
            $this->lang_code
        );

        if (!empty($params['employees']) && $params['employees'] === true) {
            $employee_ids = $this->getLinks(array_keys($departments));
            foreach ($departments as $department_id => $_department) {
                $departments[$department_id]['employee_ids'] = $employee_ids[$department_id];
            }
        }

        foreach ($departments as $department_id => $_department) {
            $departments[$department_id]['main_pair'] = !empty($images[$department_id])
                ? reset($images[$department_id])
                : [];
        }

        /**
         * Actions after getting departments list
         *
         * @param array $departments Departments list
         * @param array $params Params list
         */
        fn_set_hook('get_departments_post', $users, $params);

        return [$departments, $params];
    }

    /**
     * Get department by id
     *
     * @param int $department_id Id of the department
     *
     * @return array Department data
     */
    public function get($department_id = 0)
    {
        if (empty($department_id)) {
            return [];
        }

        $department = [];

        [$departments] = $this->getList([
            'department_id' => $department_id,
            'employees' => true
        ]);

        if (!empty($departments)) {
            $department = reset($departments);
        }

        return $department;
    }

    /**
     * Update department/Add department by id
     *
     * @param mixed[] $data New department data
     *
     * @param int $department_id Id of the department
     *
     * @return mixed department_id or false
     */
    public function upsert($data, $department_id)
    {
        array_walk($data, 'fn_trim_helper');

        $is_department_empty = empty($data['department']);
        $is_supervisor_empty = empty($data['supervisor_id']);

        if ($is_department_empty || $is_supervisor_empty) {
            return false;
        }

        if (!empty($department_id)) {
            $this->db->query(
                'UPDATE ?:departments SET ?u WHERE department_id = ?i',
                $data,
                $department_id
            );

            $this->db->query(
                'UPDATE ?:department_descriptions SET ?u WHERE department_id = ?i ' .
                    'AND lang_code = ?s',
                $data,
                $department_id,
                $this->lang_code
            );
        } else {
            $data['timestamp'] = time();
            $department_id = $this->db->replaceInto(
                'departments',
                $data
            );

            $data_with_languages = [];
            foreach (Languages::getAll() as $lang_code => $_v) {
                $data_with_languages[] = [
                    'department_id' => $department_id,
                    'department' => $data['department'],
                    'description' => $data['description'],
                    'lang_code' => $lang_code
                ];
            }
            $this->db->replaceInto(
                'department_descriptions',
                $data_with_languages,
                true
            );
        }

        if (!empty($department_id)) {
            fn_attach_image_pairs(
                'department',
                'department',
                $department_id,
                $this->lang_code
            );
        }

        $employee_ids = !empty($data['employee_ids']) ? $data['employee_ids'] : [];

        $this->deleteLinks($department_id);
        $this->addLinks(
            $department_id,
            $data['supervisor_id'],
            $employee_ids
        );
        return $department_id;
    }

    /**
     * Update multiple departments by data
     *
     * @param mixed[] $department_data Array of arrays with department id as a key
     * and department data as a value
     *
     * @return void|false false if something goes wrong
     */
    public function updateMultiple($department_data)
    {
        if (empty($department_data)) {
            return false;
        }

        $queries = [];

        foreach ($department_data as $id => $data) {
            $queries[] = [
                MultiQueryTypes::QUERY,
                $this->db->quote(
                    'UPDATE ?:departments SET ?u WHERE department_id = ?i',
                    $data,
                    $id
                )
            ];

            $queries[] = [
                MultiQueryTypes::QUERY,
                $this->db->quote(
                    'UPDATE ?:department_descriptions SET ?u WHERE department_id = ?i ' .
                        'AND lang_code = ?s',
                    $data,
                    $id,
                    $this->lang_code
                )
            ];
        }

        $this->db->multiQuery($queries);

        $this->deleteLinks(array_keys($department_data));

        $this->addMultipleLinks($department_data);

        Registry::cleanup();
    }

    /**
     * Get users with only necessary fields
     *
     * @param $params Params
     * @param $userIds Ids of needed users
     */
    public function getUsers($params, $userIds)
    {
        $params['user_id'] = $userIds;

        // It is necessary to query for only needed users fields through hook
        $params['sd_departments_users_field_limit'] = true;

        return fn_get_users($params, Tygh::$app['session']['auth']);
    }

    /**
     * Remove department by id
     *
     * @param int|int[] $department_id Id or ids of the department
     *
     * @return void
     */
    public function delete($department_id)
    {
        if (empty($department_id)) {
            return;
        }

        $this->castToArray($department_id);

        $this->db->query(
            'DELETE FROM ?:departments WHERE department_id IN (?n)',
            $department_id
        );

        $this->deleteImagePairs($department_id);
        $this->deleteLinks($department_id);
    }

    /**
     * Deletes image pair by id or ids
     *
     * @param int|int[] $department_id Id or ids of department
     *
     * @return void
     */
    public function deleteImagePairs($department_id)
    {
        if (empty($department_id)) {
            return;
        }

        $this->castToArray($department_id);

        foreach ($department_id as $id) {
            fn_delete_image_pairs(
                $id,
                'department',
                ImagePairTypes::MAIN
            );
        }
    }

    /**
     * Update departments status
     *
     * @param string $status_to Update status to
     * @param int[] $department_ids Ids of departments
     *
     * @return bool
     */
    public function updateStatuses($status_to, $department_ids)
    {
        if (empty($department_ids) || empty($status_to)) {
            return false;
        }

        return db_query(
            'UPDATE ?:departments SET status = ?s WHERE department_id IN (?n)',
            $status_to,
            $department_ids
        );
    }

    /**
     * Get and set supervisor info to departments by reference
     *
     * @param array<mixed[]> $departments Reference to departments array
     *
     * @param mixed[] $params Optional. Parameters to user query
     *
     * @return void departments will be modified by reference
     */
    public function setSupervisorInfo(&$departments, $params = [])
    {
        $supervisor_ids = array_column($departments, 'supervisor_id');
        [$supervisors] = $this->getUsers($params, $supervisor_ids);

        $supervisors_with_keys_as_id = [];

        foreach ($supervisors as $supervisor) {
            $supervisors_with_keys_as_id[$supervisor['user_id']] = $supervisor;
        }

        foreach ($departments as &$department) {
            $supervisor_id = $department['supervisor_id'];
            $department['supervisor_info'] = $supervisors_with_keys_as_id[$supervisor_id] ?? 'Неизвестный руководитель';
        }
    }

    /**
     * Gets all department ids
     *
     * @return int[] ids of departments
     */
    public function getAllIds()
    {
        return $this->db->getColumn('SELECT department_id FROM ?:departments WHERE 1');
    }

    /**
     * Casts value to array if it's not already array
     *
     * @param mixed $value Value to cast
     *
     * @return void
     */
    protected function castToArray(&$value)
    {
        $value = is_array($value) ? $value : [$value];
    }

    /**
     * Get multiple department links by department ids
     *
     * @param int[] $departments_ids Ids of departments
     *
     * @return array<int[]> Array of arrays with departments id as key
     * and employees as values
     */
    protected function getLinks($department_ids)
    {
        if (empty($department_ids)) {
            return [];
        }

        $queries = [];

        foreach ($department_ids as $id) {
            $queries[$id] = [
                MultiQueryTypes::COLUMN,
                $this->db->quote(
                    'SELECT employee_id FROM ?:department_links WHERE department_id = ?i',
                    $id
                )
            ];
        }

        return $this->db->multiQuery($queries);
    }

    /**
     * Add department links by id
     *
     * @param int $department_id Id of the department
     *
     * @param int $supervisor_id Id of the department supervisor
     *
     * @param string $employee_ids Employee ids of the current department
     *
     * @return void
     */
    protected function addLinks($department_id, $supervisor_id, $employee_ids)
    {
        if (empty($employee_ids) || empty($department_id)) {
            return;
        }

        $employee_ids = explode(',', $employee_ids);
        $department_links = [];
        foreach ($employee_ids as $employee_id) {
            if ($employee_id !== $supervisor_id) {
                $department_links[] = [
                    'department_id' => $department_id,
                    'employee_id' => $employee_id
                ];
            }
        }

        $this->db->replaceInto('department_links', $department_links, true);
    }

    /**
     * Add multiple department links by data
     *
     * @param mixed[] $department_data Array of arrays with department id as a key
     * and department data as a value
     *
     * @return void
     */
    protected function addMultipleLinks($department_data)
    {
        if (empty($department_data)) {
            return;
        }

        $department_links = [];

        foreach ($department_data as $id => $data) {
            if (empty($data['employee_ids'])) {
                continue;
            }

            $employee_ids = explode(',', $data['employee_ids']);

            foreach ($employee_ids as $employee_id) {
                if ($employee_id !== $data['supervisor_id'] ?? '') {
                    $department_links[] = [
                        'department_id' => $id,
                        'employee_id' => $employee_id
                    ];
                }
            }
        }


        $this->db->replaceInto('department_links', $department_links, true);
    }

    /**
     * Remove department links by id
     *
     * @param int|int[] $department_id Id or ids of the department
     *
     * @return void
     */
    protected function deleteLinks($department_id)
    {
        if (empty($department_id)) {
            return;
        }

        $this->castToArray($department_id);

        $this->db->query(
            'DELETE FROM ?:department_links WHERE department_id IN (?n)',
            $department_id
        );
    }
}
