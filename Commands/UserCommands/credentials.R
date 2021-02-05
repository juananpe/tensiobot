library(yaml)
config = yaml.load_file("config.yml")
realm <- config$realm
ifelse(realm=="dev", config<-config$dev, config<-config$prod)
user<-config$dbuser
password<-config$dbpassword
dbname<-config$dbname
host<-config$dbhost
port<-config$dbport
workdir<-config$workdir

