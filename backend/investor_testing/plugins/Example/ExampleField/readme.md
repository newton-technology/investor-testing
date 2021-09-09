Плагин добавляет ссылку на логотип категории в ответы методов `GET /categories`.

```
$ curl -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJuZXd0b24tdGVjaG5vbG9neVwvaW52ZXN0b3JfdGVzdGluZyIsImF1ZCI6Im5ld3Rvbi10ZWNobm9sb2d5XC9pbnZlc3Rvcl90ZXN0aW5nXC9hY2Nlc3MiLCJpYXQiOjE2Mjg3NzUxNTcsImV4cCI6MTYyODc3NTc1Nywic3ViIjoiMSJ9.xr4IA8CpifQsQUrFQJd_yRX2y8NJNLweeIV7niPj-aJgPGMCLN77geedhbqcpeqb5QrIgX9FpjNbhzkBR7J0ZE5mKD23tZYfJeECWVqgaIEDocL1wODWgfJDdVS24vyPD8SKUwxM5i244IKIdaGCVc56KxEkzOI4kNjLExxX7VDlrW5-4FLBiVjUwilASuQmHQXjKLzbDm4sjB6HaH2-5BEtOqNOwLFd-IYLs6U5SgWFotFajFbUqQLDN7JJNAwxu0CG-XDrwMJukiD10vbqNjOyByKfbO4rWz2277NNiKFet1-F196pmnBLea1Rm-K1SXsULJH_LHbYVsu5pHTwMg" http://localhost:9000/api/investor_testing/categories/2
{"category":{"logo":"\/resources\/category2.png","id":2,"code":"category2","name":"Категория 2","description":"Договоры, являющиеся производными финансовыми инструментами и не предназначенные для квалифицированных инвесторов","descriptionShort":"Производные финансовые инструменты"},"status":null}
```
