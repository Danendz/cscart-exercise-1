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

namespace Tygh\Addons\SdDepartments;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\SdDepartments\HookHandlers\UsersHookHandler;
use Tygh\Addons\SdDepartments\Users\Departments;
use Tygh\Tygh;

/**
 * Class ServiceProvider is intended to register services and components of the sd_departments
 * add-on to the application container
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['addons.sd_departments.profiles.departments'] = function (Container $app) {
            return new Departments($app, AREA);
        };

        $app['addons.sd_departments.hook_handlers.users'] = function (Container $app) {
            return new UsersHookHandler($app);
        };
    }

    /**
     * @return Departments
     */
    public static function getDepartmentsService()
    {
        return Tygh::$app['addons.sd_departments.profiles.departments'];
    }
}
