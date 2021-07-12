<?php
/**
 * Этот файл часть workload 
 * @package   workload
 * @copyright Copyright (c) 2021, Борис Федоров <w-navt@yandex.ru>
 * @license  /system/controllers/workload/license.txt
 */ 
class backendWorkload extends cmsBackend {
 
    protected $useOptions = true;
 
    public $useDefaultOptionsAction = true;
 
    public function actionIndex() {
        $this->redirectToAction('options');
    }
 
    public function getBackendMenu() {
        return [
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            ],
            [
                'title' => LANG_WL_OPT_RESALT,
                'url'   => href_to("/workload/display")
            ],
            [
                'title' => LANG_WL_COMPONENT_LOG,
                'url'   => href_to("/workload/log")
            ],
        ];
    }

    public function actionDeleteComponent(){

        $model = new cmsModel();
        $model->deleteController('workload');

        if (file_exists($this->cms_config->cache_path."wl.log") === true) {
            unlink($this->cms_config->cache_path."wl.log");
        }

        if (file_exists($this->cms_config->cache_path."wl.json") === true) {
            unlink($this->cms_config->cache_path."wl.json");
        }

        cmsUser::addSessionMessage(sprintf(LANG_CP_COMPONENT_IS_DELETED, LANG_WORKLOAD_CONTROLLER), 'success');
        $this->redirectTo('admin', 'controllers');

    }
}