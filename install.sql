UPDATE `{#}controllers` 
    SET `options` = '---\r\nworkload_on: null\r\nstart_date: 2021-07-01 22:30\r\nfinish_date: 2021-07-02 22:30\r\nlogging_on: null\r\nincl_on_hands: null\r\nqty_cpu: 1\r\n' 
    WHERE `{#}controllers`.`name` = 'workload';