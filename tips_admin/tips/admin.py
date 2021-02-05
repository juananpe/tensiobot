# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.contrib import admin

from django.contrib.auth.models import User
from django.contrib.auth.models import Group


# Register your models here.
from tips.models import (
    Tips, Texts,
)

admin.site.register(Tips)
admin.site.register(Texts)

admin.site.unregister(User)
admin.site.unregister(Group)
