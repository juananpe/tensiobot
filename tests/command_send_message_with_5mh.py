# -*- coding: utf-8 -*-
"""
Test /tension command from tensiobot
"""
from pytg.sender import Sender
from colorama import Fore, Back, Style, init

__author__ = 'juananpe'
import time
import os

#import logging
#logging.basicConfig(level=logging.DEBUG)

def comprobar(res, mensaje):

    print("Comprobando mensaje '%s':" % mensaje),
    if res['text'].startswith(mensaje):
       print (Fore.GREEN + "OK")
    else:
       print (Fore.RED + "KO")




def main():

    init(autoreset=True)
    bot = os.getenv('BOT', 'tensio2bot')
    # bot = "TensioBot"
    # bot = "tensio2bot"
    sender = Sender("127.0.0.1", 9009)

    res = sender.msg(bot, "/cancel")
    time.sleep(2)
    res = sender.msg(bot, "/tension")
    time.sleep(2)
    res = sender.msg(bot, "Sí, ahora")
    time.sleep(2)
    res = sender.msg(bot, "120")
    time.sleep(2)
    res = sender.msg(bot, "90")
    time.sleep(2)
    res = sender.history(bot, 2);
    time.sleep(2)
    
    comprobar(res[1], 'Para confirmar que la tensión es estable')


    time.sleep(2)
    res = sender.msg(bot, "OK, ya he vuelto a tomarme la tensión");
    time.sleep(2)

    res = sender.msg(bot, "126")
    time.sleep(2)

    res = sender.msg(bot, "90")

    time.sleep(8)
    res = sender.history(bot, 2);

#    print("Response: {response}".format(response=res))

    comprobar(res[0], 'La diferencia entre tomas es de más de')
    comprobar(res[1], 'Para confirmar que la tensión es estable')

    time.sleep(2)
    res = sender.msg(bot, "OK, ya he vuelto a tomarme la tensión");
    time.sleep(2)

    res = sender.msg(bot, "126")
    time.sleep(2)

    res = sender.msg(bot, "90")

    time.sleep(8)
    res = sender.history(bot, 2);

    comprobar(res[0], 'Muy bien')
    comprobar(res[1], 'Cuando quieras')

  
# end def main

if __name__ == '__main__':
    main()
