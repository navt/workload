<?php
 
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

        cmsUser::addSessionMessage(sprintf(LANG_CP_COMPONENT_IS_DELETED, LANG_WL_CONTROLLER), 'success');
        $this->redirectTo('admin', 'controllers');

    }
}