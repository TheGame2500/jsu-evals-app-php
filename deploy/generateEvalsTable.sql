-- get colums
SELECT 
    GROUP_CONCAT(ids.field_id)
from
    (SELECT 
        CASE
                WHEN
                    (META_KEY RLIKE '_field_.*'
                        AND META_VALUE IS NOT NULL)
                THEN
                    REPLACE(META_KEY, '_field_', '')
            END as FIELD_ID
    FROM
        d5a_postmeta
    WHERE
        POST_ID = (SELECT 
                max(post_id)
            FROM
                d5a_postmeta)
    ORDER BY post_id DESC) ids;

SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(data, '{',','),'}',','),';',','),'"',','),':',','),',,',','),',,,,',',') as list from d5a_ninja_forms_fields where id=12;