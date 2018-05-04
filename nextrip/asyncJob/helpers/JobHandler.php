<?php

namespace nextrip\asyncJob\helpers;

abstract class JobHandler {
    
    /**
     * @param \nextrip\asyncJob\models\AsyncJob
     */
    public static abstract function run($job);
    
    
} 

