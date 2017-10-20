CREATE TABLE its_hackathon.wald_streckennetz_verschnitt AS
WITH wald_polys AS (
  SELECT
    cl.*
  FROM
    its_hackathon.corine_landcover_10 cl
  WHERE
    cl.clc in ('311', '312','313')
),
gleise_within AS (
  SELECT
    db.*
  FROM
    wald_polys  c,
    its_hackathon.db_streckennetz db
  WHERE
    ST_Within(db.geom, c.geom)
),
gleise_intersects AS (
  SELECT
    db.*
  FROM
    wald_polys  c,
    its_hackathon.db_streckennetz db
  WHERE
    ST_Intersects(c.geom, db.geom)
),
intersection AS (
  SELECT
    db.id,
    ST_Intersection(db.geom, wp.geom) AS geom,
    db.mifcode,
    db.strecke_nr,
    db.richtung,
    db.laenge,
    db.von_km_i,
    db.bis_km_i,
    db.von_km_l,
    db.bis_km_l,
    db.elektrifiz,
    db.bahnnutzun,
    db.geschwindi,
    db.strecke_ku,
    db.gleisanzah,
    db.bahnart,
    db.kmspru_typ,
    db.kmspru_t00
  FROM
    gleise_intersects db,
    wald_polys wp
)
SELECT g.geom FROM gleise_within g
UNION
SELECT geom FROM intersection;
