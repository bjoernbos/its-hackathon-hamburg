
-- Bounding box vom strassenbaumkataster-Layer

DROP TABLE its_hackathon.hamburg_bounds;

CREATE TABLE its_hackathon.hamburg_bounds AS
SELECT
  ST_Envelope(ST_Collect(geom)) AS geom
FROM
  its_hackathon.strassenbaumkataster;