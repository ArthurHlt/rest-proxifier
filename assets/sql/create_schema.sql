CREATE TABLE IF NOT EXISTS %s (
route VARCHAR(255) NOT NULL,
api VARCHAR(255) NULL,
request_header TEXT NULL,
response_header TEXT NULL,
response_content TEXT NULL,
PRIMARY KEY (route)
);