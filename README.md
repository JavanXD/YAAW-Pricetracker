

# YAAW.de -  Yet Another AmazonWatcher
YAAW.de is a price monitoring service for Amazon products that informs you about positive price changes of your desired products.

![YAAW-LOGO](assets/img/AmazonWatcher3.png)

## Usage
- For the registration only the **E-Mail-address** is needed.
- Amazon products can then be saved with the indication of their **URL**.
- Another input field allows you to enter a specific **desired price**, or a **upper price limit** for the product. 
- If the price falls below this value, a **notification** is automatically sent to the corresponding e-mail.
- A list of the monitored products allows a quick overview of the **price history**. 

## Try it live
* Try it [LIVE](https://www.yaaw.de/).
* See also our [Android-App](https://play.google.com/store/apps/details?id=de.javan.yaaw).

```
 __     __                       __          __
 \ \   / /     /\         /\     \ \        / /
  \ \_/ /     /  \       /  \     \ \  /\  / / 
   \   /     / /\ \     / /\ \     \ \/  \/ /  
    | |     / ____ \   / ____ \     \  /\  /   
    |_|    /_/    \_\ /_/    \_\     \/  \/     
    
```

## Used technologies
* PHP used for back-end
* MySql used for database
* jQuery, Fontawesome and Bootstrap4 used for front-end

## Contribute
I'm happy about pull requests. I do not actively develop the project because it already meets the basic requirements for a price guard. However, I would be happy about an internationalization. Translations would have to be added. Currently YAAW.de is mostly for the Amazon.de market with german messages.
### Roadmap (To-Dos)
* Multilanguage support 

## Setup your own pricetracker for amazon

1. Create MySql Database. Import `\docs\Database\yaaw.sql`
2. Register Amazon account and create [AWS API Keys](https://console.aws.amazon.com/iam/home?region=us-west-2#/security_credential).
3. Create `secrets.php` into `\core`.
    ```
    <?php
    // Amazon secrets
    define('AWS_PUBLIC_KEY', 'AWS_PUBLIC_KEY');
    define('AWS_PRIVATE_KEY', 'AWS_PRIVATE_KEY');
    define('ASSOCIATE_TAG', 'yaaw-21'); // Put your Affiliate Code here
    
    // MySql secrets
    define('DB_HOST', 'localhost');
    define('DB_USER', 'DB_USER');
    define('DB_PASSWORD', 'DB_PASSWORD');
    define('DB_DATABASE', 'DB_DATABASE');
    
    // Common
    define('CORS', 'https://www.yaaw.de');
    define('WATCHER_URI', 'https://www.yaaw.de');
    define('ADMIN_PASSWORD', 'ADMIN_PASSWORD');
    ?>
    ```
4. Add Cronjob to enable price tracking and email notifications.
    ```
    * * * * * /usr/bin/curl -m 59 -s 'yourhost/core/control/autoload.php?admin=ADMIN_PASSWORD' &>/dev/null
    ```
5. Check `url` variable within `\assets\js\app.js` to locate the running backend.
