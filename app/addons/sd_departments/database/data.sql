REPLACE INTO ?:departments (status, timestamp, supervisor_id) VALUES ('A', UNIX_TIMESTAMP(NOW()), 3);
REPLACE INTO ?:departments (status, timestamp, supervisor_id) VALUES ('A', UNIX_TIMESTAMP(NOW()), 4);
REPLACE INTO ?:departments (status, timestamp, supervisor_id) VALUES ('A', UNIX_TIMESTAMP(NOW()), 5);
REPLACE INTO ?:departments (status, timestamp, supervisor_id) VALUES ('A', UNIX_TIMESTAMP(NOW()), 6);
REPLACE INTO ?:departments (status, timestamp, supervisor_id) VALUES ('A', UNIX_TIMESTAMP(NOW()), 7);

REPLACE INTO ?:department_descriptions (department_id, lang_code, department, description) VALUES (1, 'ru', 'Администраторы', 'Это наши великолепные администраторы!');
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (2, 'ru', 'Сеошники', 'Это наши замечательные сеошники!');
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (3, 'ru', 'Менеджеры', 'Это наши незаменимые менеджеры!');
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (4, 'ru', 'Программисты', 'Это наши лучшие программисты!');
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (5, 'ru', 'Руководители', 'Это наши прекрасные руководители!');

REPLACE INTO ?:department_links (department_id, employee_id) VALUES (1, 4);
REPLACE INTO ?:department_links (department_id, employee_id) VALUES (1, 5);
REPLACE INTO ?:department_links (department_id, employee_id) VALUES (2, 6);
REPLACE INTO ?:department_links (department_id, employee_id) VALUES (2, 7);
REPLACE INTO ?:department_links (department_id, employee_id) VALUES (3, 8);
REPLACE INTO ?:department_links (department_id, employee_id) VALUES (3, 9);
REPLACE INTO ?:department_links (department_id, employee_id) VALUES (4, 10);
REPLACE INTO ?:department_links (department_id, employee_id) VALUES (5, 11);