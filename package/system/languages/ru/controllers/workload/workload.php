<?php
define("LANG_WORKLOAD_CONTROLLER",  "Рабочая нагрузка на сервер");
define("LANG_WL_TAB_ONE",     "Управление");
define("LANG_WL_SWITCHED_ON", "Компонент включён");
define("LANG_WL_LOGGING_ON",  "Логирование включёно");
define("LANG_WL_START_DATE",  "Начать наблюдения с момента");
define("LANG_WL_FINISH_DATE", "Прекратить наблюдения с момента");

define("LANG_WL_ON_HINT", 
    "Перед новым циклом сбора статитстики выключите и сохраните для рестарта");
define("LANG_WL_START_HINT",  "Время в формате ГГГГ-ММ-ДД ЧЧ:ММ");
define("LANG_WL_FINISH_HINT", "Время в формате ГГГГ-ММ-ДД ЧЧ:ММ");
define("LANG_WL_LOG_HINT", 
    "Включите логгирование, если что-то пошло не так. Смотрите /cache/wl.log");

define("LANG_WL_TAB_CPU",     "Процессоры / Загрузка");

define("LANG_WL_INCL_ON_HANDS", "Определить количество процессоров вручную");
define("LANG_WL_QTY_CPU",     "Количество процессоров");
define("LANG_WL_OVERLOAD_FIXING", "Фиксировать случаи критичной загрузки системы");
define("LANG_WL_OVERLOAD_VALUE",  "Значение критичной загрузки в %");

define("LANG_WL_INCL_HINT",   "Используйте, если автоматическое определение не работает");
define("LANG_WL_CPU_HINT",    "Количество процессоров на вашем сервере");
define("LANG_WL_OVERLOAD_FIXING_HINT", 
    "Случаи загрузки системы, превышающую критичную, будут писаться в лог, если он включен");
define("LANG_WL_OVERLOAD_VALUE_HINT",  
    "Если текущее значение загрузки будет больше указанного, то будет фиксация");

define("LANG_WL_OPT_RESALT",  "Отчет");
define("LANG_WL_COMPONENT_LOG",  "Лог компонента");