select
a.id,
a.strecke_ku,
a.bahnnutzun,
a.elektrifiz,
st_intersection(a.geom, b.geom) as geom

from
its_hackathon.hh_admin b,
its_hackathon.db_streckennetz a