-- really ugly hack for processing a string of values separated by commas
-- also really ugly hack to have them separated by commas and not other symbols
DROP TABLE IF EXISTS EVALS_TABLE;

SET @sql=null;
SELECT group_concat(DISTINCT CONCAT(REPLACE(REPLACE(columns.column_name,' ','_'),'?','')))
FROM 
	(SELECT 
			replace(substring(substring_index(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(data, '{', ','), '}', ','), ';', ','), '"', ','), ':', ','), ',,', ','), ',,,,', ','), ',', 8), length(substring_index(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(data, '{', ','), '}', ','), ';', ','), '"', ','), ':', ','), ',,', ','), ',,,,', ','), ',', 8 - 1)) + 1), ',', '') as Column_Name
		from
			d5a_ninja_forms_fields
		where
			id in (SELECT 
					CASE
							WHEN
								(META_KEY RLIKE '_field_.*'
									AND META_VALUE != '')
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
				ORDER BY post_id DESC)) columns into @sql;

SET @sql = CONCAT('CREATE TABLE EVALS_TABLE( id int,', REPLACE(@sql,',',' varchar(255),'),' varchar(255))');
PREPARE smtp from @sql;
EXECUTE smtp;
DEALLOCATE PREPARE smtp;
