News Yii2
============================

Task time: about 20 hours. <br />
CV: [https://linkedin.com/in/kimvladis](https://linkedin.com/in/kimvladis) <br />
Demo: [http://vladislav.kim](http://vladislav.kim) <br />

CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=news',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

### Events

Add events, that you want to listen, to `components/EventsBootstrap::bootstrap()` method, for example:
```php
public function bootstrap($app)
{
    // ...
    $this->add(
        DbUser::className(),
        ActiveRecord::EVENT_AFTER_UPDATE,
        'userVerified',
        function ($data) {
            return (isset($data->changedAttributes['verified']) && $data->changedAttributes['verified'] == 1);
        }
    );
    // ...
}
```

The event `userVerified` would be triggered after the user's attribute changed to `1`.
Notifications of this event you can describe in `Notifications manager` panel.