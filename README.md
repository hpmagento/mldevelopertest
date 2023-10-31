This module enables the admin to restrict products at a product level, It is allow the admin to BLOCK the product from being ordered from one or more
countries.
Used <a href="https://github.com/ipinfo/php">IPinfo PHP Client Library</a> to retrieve the country from the visitors IP to obtain the country for the current customer.

**<h2>Download & Install</h2>**

You can download from the following resouces:

<ul><li><a href="https://github.com/hpmagento/mldevelopertest">Github</a></li></ul>

<h3>Install via composer (recommend)</h3>
<p>composer require mltest/module-developertest:dev-master</p>
<p>composer require ipinfo/ipinfo:2.2.0</p>
<p>php bin/magento setup:upgrade</p>
<p>php bin/magento setup:di:compile</p>
<p>php bin/magento setup:static-content:deploy -f</p>


**<h2>User Guide</h2>**

**<h3>How to config</h3>**
Login to the Magento admin, choose Stores > Settings > Configuration > ML DevelopmentTest > Block Product.
<p>
<h4>1. Block Product Configurations</h4>
<ul><li>In the Enable field, select “Yes” to enable this extension.</li>
<li>In the Error Message field, Enter the error message, It will show when customer will not eligible to buy.</li>
</p>
</ul>
<p>
<h4>2.IPinfo Configurations</h4>
<ul><li>In Access Token, You'll need an IPinfo API access token, which you can get by signing up for a free account at https://ipinfo.io/signup.</li></ul>
</p>
<p>
<h3>How to Block Product At a product level</h3>
<ul><li>Login to the Magento admin, choose Catalog > Products. From product listing select the product and edit it, Then check "Block product by countries" attribute and select countries.</li></ul> 
</p>
<h3>Note</h3>
<p>This extension is tested on magento version 2.4.6-p3</p>
<p>Review recorded of testing this extension in magento version 2.4.6-p3 : <a target="_blank" href="https://www.awesomescreenshot.com/video/22064580?key=e5a81072ae7a3be4f8255ee257fc3060">Link</a></p>
