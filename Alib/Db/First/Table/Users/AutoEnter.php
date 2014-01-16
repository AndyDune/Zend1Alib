<?php
/**
 * 
 *
 *
CREATE TABLE "am_users_base_auto-enter" (
    key character varying(50) NOT NULL,
    password character varying(50),
    user_id integer,
    date date DEFAULT now()
);

ALTER TABLE ONLY "am_users_base_auto-enter"
    ADD CONSTRAINT "am_users_base_auto-enter_pkey" PRIMARY KEY (key);
 * 
 * 
 */

namespace Alib\Db\First\Table\Users;
use Alib\Db\First as First;
class AutoEnter extends First\Abs\Table
{
    
}