<?php
 
class formWorkloadOptions extends cmsForm {
 
    public $is_tabbed = true;
 
    public function init() {
 
        return [
            [
                'type' => 'fieldset',
                'title' => LANG_WL_TAB_ONE,
                'childs' => [
                     new fieldCheckbox('workload_on', [
                        'title' => LANG_WL_SWITCHED_ON,
                        'default' => null,
                        'hint' => LANG_WL_ON_HINT
                    ]),
                    new fieldString('start_date', [
                        'title' => LANG_WL_START_DATE,
                        'default' => '2021-08-22 19:30',
                        'hint' => LANG_WL_START_HINT
                    ]),
                    new fieldString('finish_date', [
                        'title' => LANG_WL_FINISH_DATE,
                        'default' => '2021-08-23 19:30',
                        'hint' => LANG_WL_FINISH_HINT
                    ]),
                    new fieldCheckbox('logging_on', [
                        'title' => LANG_WL_LOGGING_ON,
                        'default' => null,
                        'hint' => LANG_WL_LOG_HINT
                    ]),
                ]
            ],
            [
                'type' => 'fieldset',
                'title' => LANG_WL_TAB_CPU,
                'childs' => [
                     new fieldCheckbox('incl_on_hands', [
                        'title' => LANG_WL_INCL_ON_HANDS,
                        'default' => null,
                        'hint' => LANG_WL_INCL_HINT
                    ]),
                    new fieldNumber('qty_cpu', [
                        'title' => LANG_WL_QTY_CPU,
                        'default' => 1,
                        'rules'   => [
                            ['number'],
                            ['min', 1]
                        ],
                        'hint' => LANG_WL_CPU_HINT
                    ]),
                ]
            ],
        ];
 
    }
 
}