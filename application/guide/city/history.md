#20.10.2017
Версия 1.2.5
###Главное окно -> Жильцы без карты
Выводится список пользователей, у которых нет карты. При переходе по ссылке есть возможность удалить выбранных жильцов из базы данных СКУД.
###Статистика по событиям
Введ анализ событий вида Недействительная карта и указана причина отказа.
#15.08.2017
Версия 1.2.4
###Главное окно -> Список карт с прошедшим сроком действия
Добавлены колонки:  
- Просрочена, дн - указывает количество дней между текущей датой и сроком действия карты.  
- Активность - указывает статус карты: 0 - не активна, 1 - активна.
При выводе списка карт с истекшим сроком действи предлагается отметить нужные ФИО и выбрать действия:  
- удалить пользователя.  
- продлить срок действия карты до указанной даты.  
- снять картам метку "Активна", что приводит к их удалению из контроллеров.  
- добавлена возможность сортировки по колонкам.
###Сортировка данных в таблицах  
Реализована сортировка данных в таблицах. Достаточно кликнуть по заголовку, чтобы данные в колонке были отсортированы.
###Авторизация 
При авторизации логин и пароль берутся из базы данных СКУД.  
###Сортировки данных в таблицах.  
Добавлена возможность сортировки данных в таблицах кликом по столбцу таблицы.    
#15.07.2017  
##Версия 1.2.3  
#####Главное окно  
В файл конфигурации вынесены настройки, позволяющие выключать окна №№ 4,5 и панель Статистика событий.
#####Контроллеры
Добавлены значения Количество карт по базе данных
#####Жильцы  
Добавлен срок действия карты  
#####Очередь загрузки  
Добавлены колонки с меткой о том, что карта находится в очереди на загрузку.  
#####Результат загрузки карты в контроллер 
Добавлены название контроллера, номер ячейки, door.  
#####События за период  
Введена аналитика: правильно ли прошел пользователь или нет. 
#####Вычитка карт из контроллера  
Исключен обрыв связи из-за исчерпания количества сокетов (4000).  
#####Изменения в базе данных  
Добавлены PK в таблицу cardidx, что повысило быстродействие связанных процедур.  
Исправлена процедура DEVICE_UNUSEDATACCESS, в результате чего устранены задержки в Конфигураторе при добавлении точек прохода в категорию доступа.   
#30.06.2017  
##Версия 1.2.2  
Изменен запрос для получения данных о количестве карт для загрузки при подготовке данных для главного окна.  
Время подготовки данных сократилось с 6 секунд до 2.  
Расширен раздел документации.
#27.06.2017 
##Версия 1.2.1 
[*]
Исправлена ошибка с выводом количества карт для загрузки.

#1.06.2017  
##версия 1.0.