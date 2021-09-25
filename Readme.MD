# How to install ICSupload

Installing ICSupload

1. install github CLI

 curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo gpg --dearmor -o /usr/share/keyrings/githubcli-archive-keyring.gpg

echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null
  sudo apt update
  sudo apt install gh



  mysql -u root -p



sudo apt install gh
gh auth login
>> This requires your personal auth token.
>> this will require a "login by brwoser" which will then prompt for a code that you get from a browser to your github account
git clone https://github.com/salamander2/ICSupload.git
gh repo clone salamander2/ics_upload
ll
cd ics_upload/
mkdir files
sudo chgrp www-data files


ADd to APache:

/etc/apache2/sites-available>sudo vi 001-main-le-ssl.conf
<IfModule mod_ssl.c>
<VirtualHost *:443>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        ServerName iquark.ca
        #ServerName clarissaharwood.com
        ServerAdmin webmaster@localhost
    ServerAlias 52.86.248.232
        DocumentRoot /var/www/html
        Alias /ICSupload /home/databases/ics_upload   <<<<<<<<

And add this to apache2.conf

<Directory /home/databases/ics_upload>
     Options FollowSymLinks
     AllowOverride all
     Require all granted
     Order allow,deny
         allow from all
</Directory>

