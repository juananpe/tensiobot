# -*- coding: utf-8 -*-
"""
Test /cambiar command from tensiobot
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
    time.sleep(3)
    res = sender.msg(bot, "/cambiar")
    time.sleep(3)
    res = sender.msg(bot, "Sí")
    time.sleep(3)
    res = sender.msg(bot, "10:18")
    time.sleep(3)
    res = sender.history(bot, 2);
    comprobar(res[0], 'OK')
    res = sender.msg(bot, "Sí")
    time.sleep(3)
    res = sender.msg(bot, "18:29")
    time.sleep(5)
    res = sender.history(bot, 2);
    
    comprobar(res[1], 'Muy bien. Los datos han sido')
  
# end def main

if __name__ == '__main__':
    main()
