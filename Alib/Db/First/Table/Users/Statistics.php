<?php

/**
 * 
CREATE TABLE am_users_base_statistics (
    user_id integer NOT NULL,
    time_last_enter timestamp without time zone,
    time_last_post timestamp without time zone,
    enter_try_count integer DEFAULT 0,
    time_last_enter_try timestamp without time zone
);

ALTER TABLE ONLY am_users_base_statistics
    ADD CONSTRAINT am_users_base_statistics_pkey PRIMARY KEY (user_id);

 * 
 * 
 */
namespace Alib\Db\First\Table\Users;
use Alib\Db\First as First;
class Statistics extends First\Abs\Table
{
    
}
