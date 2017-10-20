WITH project as(
SELECT 
	baum.id as id,
	ST_Transform(ST_Project(ST_Transform(baum.geom, 4326)::geography, 20, radians(90.0))::geometry, 3035) as geom
FROM 
	its_hackathon.strassenbaumkataster_hpa as baum
)

SELECT 
	ST_MakeLine(project.geom::geometry,  baum.geom)
FROM 
	project, 
	its_hackathon.strassenbaumkataster_hpa as baum
WHERE
	project.id = baum.id