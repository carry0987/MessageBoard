# MessageBoard
A simple messageboard, use php7, mysqli, js

## Usage
You must set your **[config](./admin/condb.php#L4-L9)** first :<br />
```
define('DB_HOST', 'localhost');           //Your database host
define('DB_USER', 'root');                //Your database username
define('DB_PASSWORD', 'root');            //Your database password
define('DB_NAME', 'messageboard');        //Your database name
define('DB_PORT', '3306');                //Your database connect port
date_default_timezone_set('Asia/Taipei'); //Your Timezone
```
You must **not** edit :<br />
```
$version = 'vx.x.x';                      //Don't edit it !
```
## Support
- PHP ≧ 5.5
- Mysqli

## Feature
- You can edit your message which has been sent
- Support SSL
- Two Languages : English/Chinese
- Install automatically
- Mobile Support
- Use WYSIWYG Editor

## Demo
[Demo](https://www.nehscsa.com/test/messageboard/)

## Download
[Download](https://github.com/carry0987/MessageBoard/releases)

## Screenshots
Install
![](https://i.imgur.com/urfaIYg.jpg)
Homepage
![](http://i.imgur.com/6z82R70.jpg)
Message
![](http://i.imgur.com/Z8SgtW3.jpg)
Admin Page
![](https://i.imgur.com/uHISGPo.jpg)
Mobile Overview<br />
![](http://i.imgur.com/JTovlzE.jpg)
