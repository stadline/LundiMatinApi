<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 2/4/16
 * Time: 10:02 AM
 */

namespace Stadline\TasksBundle\Manager;


class StructurePhase {

    protected $structurePhase;

    public function __construct(StructurePhaseMatchInterface $structurePhase){
        $this->structurePhase = $structurePhase;
    }

    public function executeStructurePhaseMatch($value, $data, $sugarClient) {
        return $this->structurePhase->salesStageFilter($value, $data, $sugarClient);
    }
} 