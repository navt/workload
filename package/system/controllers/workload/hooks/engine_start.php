<?php
class onWorkloadEngineStart extends cmsAction {

    public function run() {
        
        if ($this->checkSwitches() === false) {
            return;
        }
        
        if ($this->checkInterval() === false) {
            return;
        }

        $data = $this->getData();

        if ($data === false) {
            return;
        }

        $data["total_visits"]++;
        $qtyCPU = $data["qty_cpu"];

        if (is_integer($qtyCPU) === true) {
            $la = $this->loadAverage($qtyCPU, 0);
        } else {
            $la = -1;
            $this->logger->log("warning", 
                "Средняя загрузка не может быть определена, не определено количество процессоров.");
        }

        $type = $this->findVisitorType();
        $visits = $data["visits"];

        if (array_key_exists($type, $visits) === true) {

            $data["visits"][$type]["qty"]++;

            if ($data["visits"][$type]["max_la"] < $la) {
                $data["visits"][$type]["max_la"] = $la;
            }

        } else {
            $data["visits"][$type]["qty"] = 1;
            $data["visits"][$type]["max_la"] = $la;
        }
        
        $this->saveData($data);
    }

}