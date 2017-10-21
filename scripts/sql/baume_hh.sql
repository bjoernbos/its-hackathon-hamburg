with baeume as(

select
its_hackathon.strassenbaumkataster.*,
its_hackathon.gattung.wurzelsystem

from
its_hackathon.gattung left join its_hackathon.strassenbaumkataster on its_hackathon.gattung .gattung_deutsch = its_hackathon.strassenbaumkataster.gattung_deutsch
)

select
b.*

from
baeume b,
its_hackathon.petrographie p,
its_hackathon.db_streckennetz db

where
st_dwithin(b.geom, db.geom, 20)
AND
st_within(b.geom, p.geom)
and
p.petrographie in ('Feinsand','Sand')