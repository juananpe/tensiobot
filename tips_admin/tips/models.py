# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Make sure each ForeignKey has `on_delete` set to the desired behavior.
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
from __future__ import unicode_literals

from django.db import models

class Tips(models.Model):
    tip = models.TextField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'tips'
        verbose_name_plural = "tips"

    def __unicode__(self):
        return self.tip


class Texts(models.Model):
    frase = models.TextField()

    class Meta:
        managed = False
        db_table = 'texts'
        verbose_name_plural = "textos"

    def __unicode__(self):
        return self.frase

