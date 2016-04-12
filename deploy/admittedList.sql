--admitted list script, limit by how many you want in the program
SELECT 
	ev.nume, 
	ev.prenume, 
	AVG(medie) AS medie_finala
FROM 
	marks mk,
	evals ev
WHERE ev.id = mk.form_id
GROUP BY mk.form_id 
ORDER BY medie_finala DESC 
LIMIT 250;
