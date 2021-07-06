<?php
class onWorkloadEngineStart extends cmsAction {

    public function run() {
        
        if ($this->checkSwitches() === false) return;
        
        if ($this->checkInterval() === false) return;

        $data = $this->getData();

        if ($data === false) return;

        $data["total_querys"]++;
        $qtyCPU = $data["qty_cpu"];

        if (is_integer($qtyCPU) === true) {
            $la = $this->loadAverage($qtyCPU, 0);
        } else {
            $la = -1;
            $this->logger->log("warning", 
                "Средняя загрузка не может быть определена, не определено количество процессоров.");
        }

        $type = $this->findVisitorType();
        $querys = $data["querys"];

        if (array_key_exists($type, $querys) === true) {

            $data["querys"][$type]["qty"]++;

            if ($data["querys"][$type]["max_la"] < $la) {
                $data["querys"][$type]["max_la"] = $la;
                $data["querys"][$type]["mft"] = $this->now->format("Y-m-d H:i:s");
            }

        } else {
            $data["querys"][$type]["qty"] = 1;
            $data["querys"][$type]["max_la"] = $la;
            $data["querys"][$type]["mft"] = $this->now->format("Y-m-d H:i:s");
        }
        
        $this->saveData($data);
    }

}