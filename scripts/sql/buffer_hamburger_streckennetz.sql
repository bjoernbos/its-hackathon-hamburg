DROP TABLE IF EXISTS its_hackathon.hamburg_streckennetz_buffer;

CREATE TABLE its_hackathon.hamburg_streckennetz_buffer AS
SELECT
  n.id, ST_Buffer(n.geom, 100) AS geom
FROM
  its_hackathon.db_streckennetz n,
  its_hackathon.hamburg_bounds b
WHERE
  ST_Contains(b.geom, n.geom)
  OR
  ST_Intersects(b.geom, n.geom);
