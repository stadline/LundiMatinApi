<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 2/4/16
 * Time: 9:40 AM
 */

namespace Stadline\TasksBundle\Manager;


interface StructurePhaseMatchInterface {
    public function salesStageFilter($value, $data, $sugarClient);
} 