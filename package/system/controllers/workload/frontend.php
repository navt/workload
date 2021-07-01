<?php
 
class workload extends cmsFrontend {
 
    protected $useOptions = true;
    
    public $switchedOn;      // компонент включен (true/false)
    public $loggingOn;        // логирование включено (true/false)
    public $dataPath;        // путь к файлу статистики 
    public $logPath;         // путь к файлу лога
    public $logger;          // объект класса Logger



    public function __construct() {
        parent::__construct($this->cms_core->request);

        $this->switchedOn = ($this->options["workload_on"] === null) ? false : true;

        $this->loggingOn = ($this->options["logging_on"] === null) ? false : true;
        $this->logPath = $this->cms_config->cache_path."wl.log";
        $this->logger = new Logger($this->logPath, $this->loggingOn);

        $this->dataPath = $this->cms_config->cache_path."wl.json";
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
            // $this->data = json_decode($s, true);
            $data = json_decode($s, true);
            if ($data === null) {
                $c[] = $s;
                $this->logger->log("error", "json не может быть преобразован.", $c);
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
                "total_visits" => 0,
                "visits" => [
                    "there_is_ua" => ["qty" => 0, "max_la" => 0],
                    "without_ua" => ["qty" => 0, "max_la" => 0] 
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

        $now = new DateTime();

        if ($now->format('U') >= $start->format('U') 
            && $now->format('U') < $finish->format('U')) {
            return true;
        }

        return false;
    }

    public function findVisitorType() {
        // https://snipp.ru/php/is-bot
        $bots = [
            'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot','YandexDirectDyn',
            'YandexScreenshotBot', 'YandexImages', 'YandexVideo', 'YandexVideoParser',
            'YandexMedia', 'YandexBlogs', 'YandexFavicons', 'YandexWebmaster',
            'YandexPagechecker', 'YandexImageResizer','YandexAdNet', 'YandexDirect',
            'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika',
            'YandexNews', 'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus',
            'YandexMarket', 'YandexVertis', 'YandexForDomain', 'YandexSpravBot',
            'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB', 'YandexOntoDBAPI',
            'Googlebot', 'Googlebot-Image', 'Mediapartners-Google', 'AdsBot-Google',
            'Mail.RU_Bot', 'bingbot', 'Accoona', 'ia_archiver', 'Ask Jeeves', 
            'OmniExplorer_Bot', 'W3C_Validator', 'WebAlta', 'YahooFeedSeeker', 
            'Yahoo!', 'Ezooms', '', 'Tourlentabot', 'MJ12bot', 'AhrefsBot', 
            'SearchBot', 'SiteStatus', 'Nigma.ru', 'Baiduspider', 'Statsbot', 
            'SISTRIX', 'AcoonBot', 'findlinks', 'proximic', 'OpenindexSpider',
            'statdom.ru', 'Exabot', 'Spider', 'SeznamBot', 'oBot', 'C-T bot', 
            'Updownerbot', 'Snoopy', 'heritrix', 'Yeti', 'DomainVader', 'DCPbot', 
            'PaperLiBot'
        ];

        if (!isset($_SERVER["HTTP_USER_AGENT"])) {
            return "without_ua";
        }

        $ua = $_SERVER["HTTP_USER_AGENT"];
        foreach ($bots as $type) {
            if (stripos($ua, $type) !== false) {
                return $type;
            }
        }

        $dt = cmsRequest::getDeviceType();
        if ($dt !== null) {
            return sprintf("cms_detect_%s", $dt);
        }

        return "there_is_ua";
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