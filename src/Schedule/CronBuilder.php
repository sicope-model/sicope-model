<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-admin
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Schedule;

use Zenstruck\ScheduleBundle\Schedule;
use Zenstruck\ScheduleBundle\Schedule\ScheduleBuilder;

/**
 * Cron Process List.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class CronBuilder implements ScheduleBuilder
{
    public function buildSchedule(Schedule $schedule): void
    {
        // Set Timezone & Environment
        $schedule->timezone('UTC')->environments('prod', 'dev');

        /*$schedule
            ->addCommand('about')
            ->description('test')
            ->everyFiveMinutes();*/
    }
}
