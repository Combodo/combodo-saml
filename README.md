
# SAML

A SimpleSAML server (deployed by Stéphane) is available on customers: <https://customers.combodo.com/simplesaml/]>.

The following user accounts are available in LDAP *(For each of these users the password is equal to the login)*:
 - blee
 - cnorris

**Note**

If you want to use the SimpleSAML server on customers, you **MUST** configure the metadata to declare your iTop as a **Service Provider** for this SimpleSAML server. This decalaration is performed by editing the file `/var/www/simplesamlphp-1.16.2/metadata/saml20-sp-remote.php`. You have to add into this file a whole block of metadata corresponding to your Service Provider instance (i.e. you iTop instance).

## SAML libraries
 
 Two PHP libraries were tested to integrate with iTop: simplesaml and onelogin/phpsaml
 - **simplesaml**: the library works fine but seems impossible to separate from the whole project/application. Embedding the library in iTop would mean including the whole SimpleSAML (and its user interface!) into iTop. The whole thing is about 100 MB! **This is not an option!**
 - **onelogin/php-saml**: the library works fine, is quite lightweight (530 Kb with its own dependencies) and can be obtained via composer. This is the library used in the POC.

## combodo-saml extension

A small extension to add the SAML SSO to iTop. The extension embeds the onelogin/php-saml library and can be deployed as any other iTop extension.

The extension extends the login page and provides 2 extra pages: `acs.php` (Assertion Consumer Service, the return page after a successful login by SAML) and `sls.php` (Single Logout Service, the return page after a successful logout by SAML).
