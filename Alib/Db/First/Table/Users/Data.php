<?php
/**
 * 
CREATE TABLE rznw_users_base_data (
    id integer NOT NULL,
    login character varying(100),
    login_normal character varying(100),
    password character varying(100),
    name character varying(150),
    email character varying(100),
    date_insert timestamp without time zone DEFAULT now(),
    status integer DEFAULT 0,
    secret_code character varying(32),
    type character varying(20),
    type_id character varying(50),
    email_info character varying(150)
);


COMMENT ON COLUMN am_users_base_data.status IS '0 - неподтвержденная регистрация';

CREATE SEQUENCE am_users_base_data_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE am_users_base_data_id_seq OWNED BY am_users_base_data.id;

ALTER TABLE am_users_base_data ALTER COLUMN id SET DEFAULT nextval('am_users_base_data_id_seq'::regclass);

ALTER TABLE ONLY am_users_base_data
    ADD CONSTRAINT am_users_base_data_login_normal_key UNIQUE (login_normal);


ALTER TABLE ONLY am_users_base_data
    ADD CONSTRAINT am_users_base_data_pkey PRIMARY KEY (id);
 *
 */

namespace Alib\Db\First\Table\Users;
use Alib\Db\First as First;
class Data extends First\Abs\Table
{
    
}