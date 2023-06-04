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

namespace Tygh\Addons\SdDepartments\HookHandlers;

use Tygh\Application;

/**
 * This class describes the hook handlers related to users
 *
 * @package Tygh\Addons\SdDepartments\HookHandlers
 */
class UsersHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * The "get_users" hook handler.
     *
     * Actions performed:
     *  - Keeps only necessary fields
     *
     * @see fn_get_users
     */
    public function onGetUsers($params, &$fields, $_sortings, $_condition, $_join, $_auth)
    {
        if (isset($params['sd_departments_users_field_limit'])) {
            $fields_to_keep = [
                'user_id',
                'firstname',
                'lastname',
                'email',
                'company_name',
                'phone'
            ];
            $fields = array_intersect_key($fields, array_flip($fields_to_keep));
        }
    }
}
