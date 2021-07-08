<?php
 
class workload extends cmsFrontend {
 
    protected $useOptions = true;
    
    public $switchedOn;      // компонент включен (true/false)
    public $loggingOn;        // логирование включено (true/false)
    public $dataPath;        // путь к файлу статистики 
    public $logPath;         // путь к файлу лога
    public $logger;          // объект класса Logger
    public $now;             // объект класса DateTime c текущим временем

    const WITHOUT_UA = "without_ua";
    const OTHER      = "other";

    public function __construct() {
        parent::__construct($this->cms_core->request);
        
        if (!isset($this->options["workload_on"])) {
            $this->switchedOn = false;
        } else {
            $this->switchedOn = ($this->options["workload_on"] === null) ? false : true;
        }

        if (!isset($this->options["logging_on"])) {
            $this->loggingOn = false;
        } else {
            $this->loggingOn = ($this->options["logging_on"] === null) ? false : true;
        }

        $this->logPath = $this->cms_config->cache_path."wl.log";
        $this->logger = new Logger($this->logPath, $this->loggingOn);

        $this->dataPath = $this->cms_config->cache_path."wl.json";
        $this->now = new DateTime();
    }

    public function getQtyCPU() {

        if (is_callable("exec") === false) {
            $this->logger->log("warning", "функция PHP exec() отключена.");
            return false;
        }

        $out = null;
        $rCode = null;
        exec("nproc --all", $out, $rCode);
        
        if ($rCode !== 0) {
            $this->logger->log("warning", sprintf("код возврата nproc --all %d", $rCode));
            return false;
        }
        
        $s = $out[0];

        if (is_numeric($s) === true) {
            return (int)$s;
        } else {
            return false;
        }
        
    }

    public function loadAverage($qtyCPU = 1, $sensor = 0) {
        $la = sys_getloadavg();
        return round(100*($la[$sensor]/$qtyCPU), 1);
    }

    public function getData() {
        if (file_exists($this->dataPath) === true) {
            // считываем статистику из файла в массив
            $s = file_get_contents($this->dataPath);
            $data = json_decode($s, true);
            if ($data === null) {
                $c["data_string"] = $s;
                $this->logger->log("error", "json не может быть преобразован.\n", $c);
                return false;
            }

        } else {
            // инициализируем массив статистики
            $qtyCPU = ($this->options["incl_on_hands"] === null) ?
                $this->getQtyCPU() : $this->options["qty_cpu"];
             
            $data = [
                "start_date" => $this->options["start_date"],
                "finish_date" => $this->options["finish_date"],
                "qty_cpu" => $qtyCPU,
                "total_querys" => 0,
                "querys" => [
                    // qty - quantity, max_la - max load  average, mft - maximum fixation time
                    self::WITHOUT_UA => ["qty" => 0, "max_la" => 0, "mft" => "2000-20-10 00:00:00"],
                    self::OTHER      => ["qty" => 0, "max_la" => 0, "mft" => "2000-20-10 00:00:00"] 
                ],
            ];
        }

        return $data;
    }

    public function saveData(array $data) {
        $s = json_encode($data, JSON_PRETTY_PRINT);

        if (file_put_contents($this->dataPath, $s) === false) {
            $this->logger->log("error", sprintf("ошибка при сохранении %s", $this->dataPath));
        }
    }

    public function checkSwitches() {
        // логирование включено?
        if ($this->loggingOn === false) {

            if (file_exists($this->logPath) === true) {
                if (unlink($this->logPath) === false) {
                    $this->logger->log("error", sprintf("ошибка при удалении %s", $this->logPath));
                }
            }

        }
        // компонент включен?
        if ($this->switchedOn === false) {

            if (file_exists($this->dataPath) === true) {
                if (unlink($this->dataPath) === false) {
                    $this->logger->log("error", sprintf("ошибка при удалении %s", $this->dataPath));
                }
            }

            return false;
        }

        return true;
    }

    public function checkInterval() {
        // мы в интервале сбора статистики?
        $start = DateTime::createFromFormat('Y-m-d H:i', $this->options["start_date"]);
        if ($start === false) {
            $this->logger->log("error", sprintf("ошибка при преобразовании времени %s", 
                $this->options["start_date"]));
            return false;    
        }

        $finish = DateTime::createFromFormat('Y-m-d H:i', $this->options["finish_date"]);
        if ($start === false) {
            $this->logger->log("error", sprintf("ошибка при преобразовании времени %s", 
                $this->options["finish_date"]));
            return false;    
        }

        if ($this->now->format('U') >= $start->format('U') 
            && $this->now->format('U') < $finish->format('U')) {
            return true;
        }

        return false;
    }

    public function findVisitorType() {

        $bots = $this->executeAction("get_bots");

        if (!isset($_SERVER["HTTP_USER_AGENT"])) {
            return self::WITHOUT_UA; // визитер без UA
        }

        $ua = $_SERVER["HTTP_USER_AGENT"];
        foreach ($bots as $type) {
            if (stripos($ua, $type) !== false) {
                return $type;   // визитёр - бот
            }
        }

        $dt = cmsRequest::getDeviceType();
        if ($dt !== null) {
            return sprintf("cms_detect_%s", $dt); // одно из: desctop, mobile, tablet
        }

        return self::OTHER;  // все остальные
    }

    public function fixingOverload($la) {
        if ($this->loggingOn === false) return;

        if ($la < $this->options["overload_value"]) return;

        $context["load_average"] = $la;
        $context["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
        $context["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
        $context["QUERY_STRING"] = $_SERVER["QUERY_STRING"];
        $context["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
        $this->logger->log("info", "Фиксация факта перегрузки сервера\n", $context);
    }

    public function actionDisplay() {
        
        if (cmsUser::isAdmin() !== "1") {
            $this->redirect(href_to_home());
        }

        $data = $this->getData();
        $this->cms_template->renderPlain("outlook", ["data" => $data]);
    }
 
}

class Logger {
    private $active;
    private $logPath;
    
    public function __construct(string $logPath="", bool $active=true) {
        
        $this->active = $active;

        if ($this->active === false) return;

        $this->logPath = $logPath;    
    }

    public function log($level, $message, array $context = []) {
        
        if ($this->active === false) return;

        $date = new DateTime();
        $out = sprintf("[%s] [%s] %s %s\n",
            $date->format('Y-m-d H:i:s.v'), 
            ucfirst($level),
            $message,
            $this->displayContext($context));
        file_put_contents($this->logPath, $out, FILE_APPEND);
    }

    private function displayContext(array $context=[]) {
        return ($context == []) ? "" : json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}