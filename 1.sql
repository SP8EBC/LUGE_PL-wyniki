SELECT 
--  competitions_to_competition_mapping.competition_serial_number, 
  competition_data.competition_serial_number,
  competition_data.competition_id,
  competition_data.competition_type_name
FROM 
  public.competitions_to_competition_mapping
JOIN 
	public.competition_data ON competitions_to_competition_mapping.competition_serial_number = competition_data.competition_serial_number
WHERE 
	competitions_to_competition_mapping.cmps_name = 'III Memoriał Mariusza Warzyboka na Sankorolkach'
GROUP BY   
	competitions_to_competition_mapping.competition_serial_number,
	competition_data.competition_serial_number,
	competition_data.competition_id,
	competition_data.competition_type_name

