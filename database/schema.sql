CREATE TABLE IF NOT EXISTS foundations (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    name                  TEXT    NOT NULL,
    cnpj                  TEXT    NOT NULL UNIQUE,
    email                 TEXT    NOT NULL,
    phone                 TEXT    NOT NULL,
    supported_institution TEXT    NOT NULL
);
