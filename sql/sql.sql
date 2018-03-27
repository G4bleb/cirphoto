/**
 * @Author: Thibault Napoléon <Imothep>
 * @Company: ISEN Yncréa Ouest
 * @Email: thibault.napoleon@isen-ouest.yncrea.fr
 * @Created Date: 22-Jan-2018 - 14:10:27
 * @Last Modified: 29-Jan-2018 - 22:43:19
 */

#-------------------------------------------------------------------------------
#--- Change database -----------------------------------------------------------
#-------------------------------------------------------------------------------
use cw_cir2;

#-------------------------------------------------------------------------------
#--- Database cleanup ----------------------------------------------------------
#-------------------------------------------------------------------------------
drop table if exists comments;
drop table if exists photos;
drop table if exists users;

#-------------------------------------------------------------------------------
#--- Database creation ---------------------------------------------------------
#-------------------------------------------------------------------------------
create table users
(
  login varchar(20) not null,
  password varchar(40) not null,
  token varchar(20),
  primary key(login)
)
engine = innodb;

create table photos
(
  id int not null auto_increment,
  title varchar(20) not null,
  small varchar(128) not null,
  large varchar(128) not null,
  primary key(id)
)
engine = innodb;

create table comments
(
  id int not null auto_increment,
  userLogin varchar(20) not null,
  photoId int not null,
  comment varchar(256) not null,
  primary key(id),
  foreign key(userLogin) references users(login),
  foreign key(photoId) references photos(id)
)
engine = innodb;

#-------------------------------------------------------------------------------
#--- Populate databases --------------------------------------------------------
#-------------------------------------------------------------------------------
insert into users(login, password) values('cir2', '3d4d09dc5332618173b0a2a5c3d06f0cc9f89468');
insert into photos(title, small, large) values('Rituel du temple', 'img/small/photo1.png', 'img/large/photo1.png');
insert into photos(title, small, large) values('Batons de prière', 'img/small/photo2.png', 'img/large/photo2.png');
insert into photos(title, small, large) values('Containers d''été', 'img/small/photo3.png', 'img/large/photo3.png');
insert into photos(title, small, large) values('Ouverture de porte', 'img/small/photo4.png', 'img/large/photo4.png');
insert into photos(title, small, large) values('Amarage en liberté', 'img/small/photo5.png', 'img/large/photo5.png');
insert into photos(title, small, large) values('Volet ouvert', 'img/small/photo6.png', 'img/large/photo6.png');
insert into photos(title, small, large) values('Repos spirituel', 'img/small/photo7.png', 'img/large/photo7.png');
insert into photos(title, small, large) values('Trois petits lapins', 'img/small/photo8.png', 'img/large/photo8.png');
insert into photos(title, small, large) values('Bienvenue chez nous', 'img/small/photo9.png', 'img/large/photo9.png');
insert into photos(title, small, large) values('Maison vers l''océan', 'img/small/photo10.png', 'img/large/photo10.png');
insert into photos(title, small, large) values('Fuite en hiver', 'img/small/photo11.png', 'img/large/photo11.png');
insert into photos(title, small, large) values('Entrée de verdure', 'img/small/photo12.png', 'img/large/photo12.png');

set autocommit = 0;
set names utf8;
