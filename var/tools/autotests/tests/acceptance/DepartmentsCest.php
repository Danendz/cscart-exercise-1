<?php

//Тестируем отделы
class DepartmentsCest
{
    public function test(AcceptanceTester $I)
    {
        //Данные администратора
        $admin_mail = 'dev@example.com';
        $admin_pass = 'root';
        $admin_name = 'Администратор Главный';

        //Отдел и его сотрудники
        $department = "Тестовый отдел";
        $employees = ["Петрова Анна", "Маслов Алексей"];

        //Попали ли мы на страницу
        $I->amOnPage('/');

        //Входим в аккаунт
        $I->click('Войти');
        $I->fillField(['id' => 'login_main_login'], $admin_mail);
        $I->fillField(['id' => 'psw_main_login'], $admin_pass);
        $I->click("form[name=main_login_form] button[type=submit]");

        //Переходим в отделы
        $I->click('Мой профиль');
        $I->click('Отделы');

        //Проверяем наличие картинки
        $I->seeElement(['class' => 'ty-pict']);

        //Проверяем название отдела
        $I->see($department);

        //Проверяем имя руководителя
        $I->see($admin_name);

        //Переходим в тестовый отдел
        $I->click($department);

        //Проверяем, что мы в тестовом отделе
        $I->see($department);

        //Проверяем наличие сотрудников тестового отдела
        foreach ($employees as $employee) {
            $I->see($employee);
        }

        //Делаем снепшот
        $I->makeHtmlSnapshot();

        //Выходим из аккаунта
        $I->click('Мой профиль');
        $I->click('Выйти');

        //Проверяем, что вышли из аккаунта
        $I->click('Мой профиль');
        $I->dontSee($admin_name);
    }
}
