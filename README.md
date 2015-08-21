# Simple Nginx Cache purger

[![Build Status](https://travis-ci.org/megumiteam/nginx-purger.svg)](https://travis-ci.org/megumiteam/nginx-purger)

It can purge caches on multiple servers.

## How to use

Set remote servers as array in wp-config.php.

```
$nginx_servers = array(
    'cat.example.com',
    'dog.example.com',
    'tiger.example.com',
);
```

Then visit [Settings] - [Nginx Purger] in WordPress Admin Screen.

![](https://www.evernote.com/l/ABWHvQZ5m_9IxY1Kt8mx-Og7hSpneZiNMz4B/image.png)

Put a URL in text box.

![](https://www.evernote.com/l/ABUgOKizJjVKfZ3g0f3gCTa510qyvtdlbIIB/image.png)

Finally, click Submit!!
