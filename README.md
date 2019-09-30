

# YAAW -  Yet Another AmazonWatcher
YAAW is a price monitoring service for Amazon products that informs you about positive price changes of your desired products.

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
I'm happy about pull requests. I do not actively develop the project because it already meets the basic requirements for a price guard. However, I would be happy about an internationalization. Translations would have to be added. 

## Setup your own pricetracker for amazon

1. Create MySql Database. Import `\docs\Database\yaaw.sql`
2. Register Amazon account and create [AWS API Keys](https://console.aws.amazon.com/iam/home?region=us-west-2#/security_credential).
3. Create `secrets.php` into `\core`.
    ```
    <?php
    // Amazon secrets
    $public_key = "public_key";
    $private_key = "private_key";
    $associate_tag  = "yaaw-21"; 
    
    // Mysql secrets
    $host = "localhost";
    $user = "root";
    $password = "password";
    $database = "database";
    ?>
    ```
4. Add Cronjob to enable price tracking and email notifications.
    ```
    * * * * * /usr/bin/curl -m 59 -s 'yourhost/core/control/autoload.php' &>/dev/null
    ```
5. Check `url` variable withing `\assets\js\app.js` to locate the running backend.
