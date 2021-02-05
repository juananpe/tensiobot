cd /opt/tg
bin/telegram-cli --json -P 9009 &

cd /opt/bots/tensiobot

while (true); do php getUpdatesCLI.php ; sleep 2; done

# Rscript Commands/UserCommands/obtenerPic.R 4694560 DEBUG


# BOT=tensio2bot python3.5 command_cambiar.py
# BOT=TensioBot python3.5 command_cambiar.py

# > contact_search TensioBot
# > msg TensioBot hola

nohup ./cron.sh >> /dev/null &

# tips admin
# tensiobot/tips_admin# nohup python manage.py runserver 0.0.0.0:8000 &

# python manage.py createsuperuser
# python manage.py makemigrations
# python manage.py migrate
# python manage.py createsuperuser
# python manage.py runserver
# python manage.py runserver 0.0.0.0:8000
# python manage.py runserver 0.0.0.0:8000
# python manage.py runserver 0.0.0.0:8000
# python manage.py runserver 0.0.0.0:8000
# python manage.py runserver 0.0.0.0:8000
# python manage.py runserver 0.0.0.0:8000
# nohup python manage.py runserver 0.0.0.0:8000 &
# python ./manage.py migrate
# nohup python manage.py runserver 0.0.0.0:8000
# python ./manage.py migrate
# python ./manage.py showmigrations
# python ./manage.py sqlmigrate 0003_texts
# python ./manage.py sqlmigrate
# python ./manage.py sqlmigrate tips 0003_texts
# python ./manage.py sqlmigrate tips 0002_auto_20170604_1913



# DOCKER
# docker exec 6dd0dfdf9e9b rm /run/apache2.pid && docker stop 6dd0dfdf9e9
#  docker ps
#  docker exec -it 42384e32543b /bin/bash
# docker rm run/apache2.pid
# docker stop 42384e32543b
# reboot
# TROUBLESHOOTING
# docker inspect IMAGE_ID
#  docker logs 42384e32543b
#  docker export 42384e32543b  > /tmp/brokecontainercontents.tar
#  cd /tmp
#  tar -tvzf brokecontainercontents.tar.gz  | grep -i pid
#  docker start 42384e32543b && docker exec 42384e32543b rm /run/apache2.pid




