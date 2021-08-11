library(RMySQL)
library(ggplot2)
library(scales) # date_breaks

source("Commands/UserCommands/credentials.R")

setwd(workdir)
Sys.setlocale("LC_ALL", 'es_ES.UTF-8')

args=(commandArgs(TRUE))
usuario <- args[1]

DEBUG = (length(args)==2)

##################
####### Funciones

# from stackoverflow https://stackoverflow.com/a/25106884/243532
wrapper <- function(x, ...) paste(strwrap(x, ...), collapse = "\n")



actualizarLastPic <- function (id){
  
  extra = ""
  if(length(id)>0){
       extra = paste0(", id = ", id)
  }
  query <- paste0("replace tensiones 
                          set valor = NOW(),
                  user_id = ", usuario, 
                  " , clave='lastpic'", extra)
  rs <- dbSendQuery(con, query)
}


generarPic <- function() {
  rs <- dbSendQuery(con, paste0("SELECT id, user_id, clave, valor, datecreated, notes
                    FROM tensiones
                    where user_id = ", usuario , 
                    " and clave in ('ta','tb')
                    order by datecreated"))
  
  yt <- dbFetch(rs)
  dbClearResult(rs)
  # sapply(yt, class)
  
  yt$no <- rep(1:nrow(yt), each = 2, length.out=nrow(yt))
  yt$user_id <- as.factor(yt$user_id)
  yt$clave <- as.factor(yt$clave)
  yt$valor <- as.integer(yt$valor)
  yt$datecreated <- as.POSIXct(yt$datecreated, format="%Y-%m-%d %H:%M:%S")
  
  # str(yt)
  # head(yt)
 
  if (nrow(yt) <= 4) {
	dateformat = 6
  	datebreaks = 1
  } else {
	dateformat = 12
        datebreaks = 6
  }

  png(paste0('imgs/', usuario, '.png'))

   if (nrow(yt)<4) {
	  picture <- ggplot(yt, aes(x=no, y=valor, colour=clave, group=1)) + 
    	   theme(axis.text.x = element_blank(), axis.title.x = element_blank()) + 
          annotate("text", x =1 , y = 150, label = wrapper( "Ez dago nahiko datu grafika marrazteko. Mesedez, sartu beste bi tensio balio grafika zuzena sortu ahal izateko.", width = 50) , size=4)
   }else{ 

  picture <- ggplot(yt, aes(x=datecreated, y=valor, colour=clave)) + geom_line() + 
	  geom_point(size=3) +
    # xlab("Medici\U00F3n") + ylab("Tensi\U00F3n") +
    xlab("Measurement") + ylab("Tentsioaren balioa") +
    scale_colour_manual("Legenda", labels=c('altua','baxua'), values=c('#F8766D','#7CAE00')) +
    theme(axis.text.x = element_text(angle=90)) + 
    scale_x_datetime(labels = date_format("%Y-%m-%d %H:%m"), 
		     breaks = yt$datecreate , 
		     date_minor_breaks = (paste0( datebreaks, " hour"))) +
    scale_y_continuous(breaks=seq(0, 260, 10))  +
    coord_cartesian(ylim=c(0, 260)) 
   }


  print(picture)
  dev.off()
}


###### 
##  Logica de negocio

con <- dbConnect(MySQL(), user=user, password=password, dbname=dbname, host=host)
# dbListTables(con)

# special group mode
rs <- dbSendQuery(con, "set sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'")

# obtener ultima fecha de creacion de la grafica
lastpic <- dbGetQuery(con, paste0("SELECT id, valor
                  FROM tensiones
                  where user_id = ", usuario ," and clave='lastpic' order by datecreated DESC LIMIT 1"))

lasttension <- dbGetQuery(con, paste0("SELECT datecreated
                  FROM tensiones
                  where user_id = ", usuario , " and clave in ('ta','tb')
                  order by datecreated desc
                  limit 1"))

if (DEBUG || (nrow(lastpic) == 0 || as.POSIXct(  lasttension$datecreated, format="%Y-%m-%d %H:%M:%S")  > as.POSIXct(lastpic$valor, format="%Y-%m-%d %H:%M:%S") )){
  generarPic()
  actualizarLastPic( lastpic$id)
}

