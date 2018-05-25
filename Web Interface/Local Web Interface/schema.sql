CREATE DATABASE projecteRPI
  COLLATE utf8_general_ci;

USE projecteRPI;

#Taula de usuaris que poden accedir al contingut multimèdia.
CREATE TABLE usuaris(
  idUsuari int AUTO_INCREMENT,
  nom VARCHAR(20) NOT NULL,
  cognoms VARCHAR(20) NOT NULL,
  correu VARCHAR(40) NOT NULL,
  usuari VARCHAR(20) NOT NULL,
  password CHAR(64) NOT NULL, #Utilitzarem sha-256 per guardar el hash de la contrasenya del usuari, per aixo hi posem CHAR(64).
  /*permis ENUM('USER', 'ADMIN') NOT NULL, #Permisos que tindrà l'usuari (USER, només podrá reproduïr multimèdia predefinits per el ADMIN,
                                                                        #ADMIN podra reproduïr i afegir multimèdia).*/
  PRIMARY KEY (idUsuari)
);

CREATE TABLE carpetes(
  idCarpeta int AUTO_INCREMENT,
  nomCarpeta VARCHAR(50) NOT NULL,
  idPropietari int NOT NULL,
  PRIMARY KEY (idCarpeta),
  CONSTRAINT FK_idPropietari_idUsuari FOREIGN KEY (idPropietari)
  REFERENCES usuaris(idUsuari)
);

#Taula que conté la ubicació del arxiu multimèdia i quin és el usuari que ha afegit el contingut.
CREATE TABLE multimedia(
  idMultimedia int AUTO_INCREMENT,
  nomVideo TEXT NOT NULL,
  localitzacio VARCHAR(50) NOT NULL, #Localització física del arxiu multimedia
  link TEXT NOT NULL,
  idPropietari int NOT NULL,
  PRIMARY KEY (idMultimedia),
  CONSTRAINT FK_idUsuari_idUsuari FOREIGN KEY (idPropietari)
  REFERENCES usuaris(idUsuari)
);

CREATE TABLE horaris(
  idHorari int AUTO_INCREMENT,
  nomHorari TEXT NOT NULL,
  hora TEXT NOT NULL,
  nomCarpetaHorari VARCHAR(50) NOT NULL,
  idPropietariHorari int NOT NULL,
  PRIMARY KEY (idHorari),
  CONSTRAINT FK_idPropietariHorari_idUsuari FOREIGN KEY (idPropietariHorari)
  REFERENCES usuaris(idUsuari)
);

CREATE TABLE raspberries(
  mac VARCHAR(20) NOT NULL,
  ipPublica VARCHAR(20) NOT NULL,
  idPropietari int,
  PRIMARY KEY (mac),
  CONSTRAINT FK_idPropietariRaspberry_idUsuari FOREIGN KEY (idPropietari)
  REFERENCES usuaris(idUsuari)
);

CREATE USER 'web'@'%' IDENTIFIED BY 'patata';
GRANT USAGE ON *.* TO 'web'@'%';
GRANT ALL PRIVILEGES ON `projecteRPI`.* TO 'web'@'%';

CREATE USER 'rpiClient'@'%' IDENTIFIED BY 'rpiClients1';
GRANT INSERT, UPDATE ON *.* TO 'rpiClient'@'%';
GRANT SELECT (mac) ON `projecteRPI`.`raspberries` TO 'rpiClient'@'%';
GRANT SELECT (ipPublica) ON `projecteRPI`.`raspberries` TO 'rpiClient'@'%';
