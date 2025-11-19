Доделал тестовое задание, добавил таблицы данных, ежедневное обновление, предусмотрел ошибки: затирания, 429. Поставил mysql на нестандартный порт(3307). Сделал добавление данных в таблицы через консоль, а так же вывел в них сообщения и логи.
Проверил получение и работоспособность. Прилагаю скрины ниже.
создание компании
<img width="1280" height="691" alt="изображение" src="https://github.com/user-attachments/assets/bce0715f-3d1b-4998-8da2-50f26390d271" />
создание apiservice
<img width="1280" height="720" alt="изображение" src="https://github.com/user-attachments/assets/45eacd51-c5c5-465b-b8f3-8f8807c0163e" />
создание типа токена
<img width="1280" height="678" alt="изображение" src="https://github.com/user-attachments/assets/c3f7e246-cc55-4bb2-b65b-1658d9899736" />
линковка api и типа токена
<img width="1280" height="698" alt="изображение" src="https://github.com/user-attachments/assets/e381d686-d10c-4c32-8651-9614a33d0ddb" />
создание аккаунта  c bearer token
<img width="1280" height="697" alt="изображение" src="https://github.com/user-attachments/assets/33a5129a-c77d-4eb7-9429-ead750d744b9" />
запрос с postman
<img width="1280" height="696" alt="изображение" src="https://github.com/user-attachments/assets/031554d3-f6dd-42f2-81a9-2f72118bc88a" />
проверка получении информации
<img width="1280" height="720" alt="изображение" src="https://github.com/user-attachments/assets/3700169d-c018-43ff-8839-1252f04d542f" />
проверка на дубликат и затирание данных
<img width="1280" height="692" alt="изображение" src="https://github.com/user-attachments/assets/f7d56d39-bb21-454f-988a-da3501929ad5" />
проверка добавления другого api для аккаунта
<img width="1280" height="693" alt="изображение" src="https://github.com/user-attachments/assets/491cd603-bfaf-4f9b-84de-04fd857ce444" />

проверка получении иноформации с токена. Был создан другой аккаунт с другим типом токена и api
<img width="1280" height="694" alt="изображение" src="https://github.com/user-attachments/assets/80d021b2-15d7-4d71-96b1-a139990dde18" />
<img width="1280" height="692" alt="изображение" src="https://github.com/user-attachments/assets/6be29a95-05e4-4bbf-a436-64778c722ac9" />
<img width="1280" height="690" alt="изображение" src="https://github.com/user-attachments/assets/52787590-f898-4d1b-96b7-92cccd61adf2" />
логи ошибки 429
<img width="1280" height="348" alt="изображение" src="https://github.com/user-attachments/assets/9f937599-49ea-4f60-8b4b-82d2f9620c5a" />
