set @sql= NULL;
Select group_concat(ev_val.value) from 
(select 
    concat('(\'',b.post_id,'\',', group_concat('\'',b.meta_value,'\''),')') as value
from
    d5a_postmeta a,
    d5a_postmeta b
where
    a.meta_key = '_form_id'
        and b.post_id = a.post_id
        and b.meta_key RLIKE ('_field_*')
        and b.meta_value != ''
group by b.post_id) ev_val into @sql;

set @sql = concat('INSERT INTO EVALS_TABLE VALUES', @sql);

prepare smtp from @sql;
execute smtp;
deallocate prepare smtp;