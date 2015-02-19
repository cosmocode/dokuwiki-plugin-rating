CREATE TEMPORARY TABLE ratingtemp (
    page,
    rater,
    lang,
    date,
    value,
    PRIMARY KEY(page, rater)
);

INSERT INTO ratingtemp (page, rater, lang, date, value) SELECT page, rater, '', '', value FROM ratings;

DROP TABLE ratings;

CREATE TABLE ratings (
    page,
    rater,
    lang,
    date,
    value,
    PRIMARY KEY(page, rater)
);

INSERT INTO ratings (page, rater, lang, date, value) SELECT page, rater, lang, date, value FROM ratingtemp;

DROP TABLE ratingtemp;
