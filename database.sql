create table if not exists users (
    id bigint(20) unsigned not null AUTO_INCREMENT,
    email varchar(255) not null,
    password varchar(255) not null,
    age tinyint(3) unsigned not null,
    country varchar(255) not null,
    social_media_url varchar(255) not null,
    created_at datetime not null DEFAULT CURRENT_TIMESTAMP(),
    updated_at datetime not null DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY(id),
    UNIQUE KEY(email)
);

create table if not exists transactions (
    id bigint(20) unsigned not null AUTO_INCREMENT,
    description varchar(255) not null,
    amount decimal(10,2) not null,
    date datetime not null,
    created_at datetime not null DEFAULT CURRENT_TIMESTAMP(),
    updated_at datetime not null DEFAULT CURRENT_TIMESTAMP(),
    user_id bigint(20) unsigned not null,
    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS receipts(
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  original_filename varchar(255) NOT NULL,
  storage_filename varchar(255) NOT NULL,
  media_type varchar(255) NOT NULL,
  transaction_id bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY(transaction_id) REFERENCES transactions (id) ON DELETE CASCADE
);